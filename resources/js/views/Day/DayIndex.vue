<template>

  <!-- there are no clothes in the DB -->
  <!-- display clothes index to add clothes -->
  <ClothIndex
    v-if="clothes.length === 0"
    :clothes = clothes
  />

  <!-- there are clothes in the DB -->
  <!-- display day index to wear clothes -->
  <div v-else-if="clothes.length > 0">



    <v-row class="fill-height">

      <v-col>

        <v-sheet height="64">

          <v-toolbar
            flat
          >

            <v-btn
              outlined
              class="mr-4"
              color="grey darken-2"
              @click="setToday"
            >
              Today
            </v-btn>

            <v-btn
              fab
              text
              small
              color="grey darken-2"
              @click="prev"
            >

              <v-icon small>
                mdi-chevron-left
              </v-icon>

            </v-btn>

            <v-btn
              fab
              text
              small
              color="grey darken-2"
              @click="next"
            >

              <v-icon small>
                mdi-chevron-right
              </v-icon>

            </v-btn>

            <v-toolbar-title v-if="$refs.calendar">
              {{ $refs.calendar.title }}
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <v-menu
              bottom
              right
            >

              <template v-slot:activator="{ on, attrs }">
                
                <v-btn
                  outlined
                  color="grey darken-2"
                  v-bind="attrs"
                  v-on="on"
                >

                  <span>{{ typeToLabel[type] }}</span>

                  <v-icon right>
                    mdi-menu-down
                  </v-icon>

                </v-btn>

              </template>

              <v-list>

                <v-list-item @click="type = 'week'">
                  <v-list-item-title>Week</v-list-item-title>
                </v-list-item>

                <v-list-item @click="type = 'month'">
                  <v-list-item-title>Month</v-list-item-title>
                </v-list-item>

              </v-list>

            </v-menu>

          </v-toolbar>

        </v-sheet>

        <v-sheet height="600">

          <v-calendar
            ref="calendar"
            v-model="focus"
            color="primary"
            :events="worn"
            :event-color="getEventColor"
            :type="type"
            interval-count=1
            interval-height="auto"
            @click:event="viewDay"
            @click:more="viewDay"
            @click:date="viewDay"
            @change="updateRange"
          ></v-calendar>

        </v-sheet>

      </v-col>

    </v-row>

    <v-dialog
      v-if="modal"
      v-model="modal"
      persistent
      @click:outside="closeViewDay()"
      width="500"
    > 

      <DayForm
        :clothes = clothes
        :currentDayInformation = currentDayInformation
      />
      
    </v-dialog>
  </div>

</template>

<script>

import DayForm from './DayForm';
import ClothIndex from '../Cloth/ClothIndex';

  export default {

    components: { DayForm, ClothIndex },

    props: {
      clothes: Array,
      days: Array
    },

    data: () => ({
      // array of clothes and dates they were worn on
      worn: [],
      // toggles DayCreateEdit modal
      modal: false,
      // object containing information used for DayCreateEdit modal
      currentDayInformation: {
        // id of date in the DB, used for deletions
        id: '',
        // date on which the cloth(es) were worn, used on Front End
        date: '',
        // Back End date format
        // used for Laravel's firstOrCreate
        backEndDate: '',
        // cloth(es) worn on a date
        worn: [],
      },

      focus: '',
      type: 'month',
      typeToLabel: {
        month: 'Month',
        week: 'Week',
      },




      
      events: [],
      colors: ['blue', 'indigo', 'deep-purple', 'cyan', 'green', 'orange', 'grey darken-1'],
      names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],

      
    }),

    methods: {

      // sets worn array
      setWorn(days){
        
        // reset previous worn data
        this.worn = [];

        // loop through all days
        days.forEach(day => {

          // set formated date
          let formatedDate = day.date.slice(0,10);

          // loop through all clothes worn on a day
          day.clothes.forEach(cloth => {

            // add to the worn array
            this.worn.push({

              // cloth name
              name: cloth.title,
              // start and end properties determine date when item was worn on
              start: formatedDate,
              end: formatedDate
              
            });
          });
        });

      },

      // views day
      viewDay ({ date, day }) {
        // sets assigned date
        this.currentDayInformation.date = date || day.date;

        // itterates through all dates
        for(let i = 0; i < this.days.length; i++){

          // looks for assigned date among existing days
          // slice removes additional backEndDate data
          if(this.currentDayInformation.date === this.days[i].date.slice(0, 10)){
            // sets day id
            this.currentDayInformation.id = this.days[i].id;

            // itterates through all clothes worn on a date
            this.days[i].clothes.forEach(cloth => {

              // adds cloth id to the worn array
              this.currentDayInformation.worn.push(cloth.id);

            });
            // stops for loop
            break;
          }
        }

        // sets Back End date
        this.currentDayInformation.backEndDate = this.currentDayInformation.date+' 00:00:00';

        // toggles modal
        this.modal = true;
      },
      
      // closes day
      closeViewDay(){
        // toggles modal
        this.modal = false;
        // resets current day information
        this.currentDayInformation = {
          date: '',
          timestamp: '',
          worn: [],
        };
      },

      getEventColor (event) {
        return 'primary';
      },

      setToday () {
        this.focus = ''
      },

      prev () {
        this.$refs.calendar.prev()
      },

      next () {
        this.$refs.calendar.next()
      },

      showEvent ({ nativeEvent, event }) {
        const open = () => {
          this.selectedEvent = event
          this.selectedElement = nativeEvent.target
          requestAnimationFrame(() => requestAnimationFrame(() => this.selectedOpen = true))
        }

        if (this.selectedOpen) {
          this.selectedOpen = false
          requestAnimationFrame(() => requestAnimationFrame(() => open()))
        } else {
          open()
        }

        nativeEvent.stopPropagation()
      },

      updateRange ({ start, end }) {
        const events = []

        const min = new Date(`${start.date}T00:00:00`)
        const max = new Date(`${end.date}T23:59:59`)
        const days = (max.getTime() - min.getTime()) / 86400000
        const eventCount = this.rnd(days, days + 20)

        for (let i = 0; i < eventCount; i++) {
          const allDay = this.rnd(0, 3) === 0
          const firstTimestamp = this.rnd(min.getTime(), max.getTime())
          const first = new Date(firstTimestamp - (firstTimestamp % 900000))
          const secondTimestamp = this.rnd(2, allDay ? 288 : 8) * 900000
          const second = new Date(first.getTime() + secondTimestamp)

          events.push({
            name: this.names[this.rnd(0, this.names.length - 1)],
            start: first,
            end: second,
            color: this.colors[this.rnd(0, this.colors.length - 1)],
            timed: !allDay,
          })
        }

        this.events = events;
        
      },
      rnd (a, b) {
        return Math.floor((b - a + 1) * Math.random()) + a
      },

      wearClothes() {
        console.log(1);
      },
    },

    beforeMount() {
      // set worn array
      this.setWorn(this.days);

      // bus methods
      EventBus.$on("setWorn", this.setWorn);
      EventBus.$on("closeViewDay", this.closeViewDay);
      
      // this.$refs.calendar.checkChange()
    },
  }
</script>
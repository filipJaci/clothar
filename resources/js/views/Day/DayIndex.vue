<template>
  <!-- there are no clothes in the DB -->
  <!-- write an appropriate message -->
  <Message
    v-if="clothes.length === 0"
    type="info"
    title="No Clothes found"
    body="There are no clothes found, please insert a cloth to continue."
  ></Message>

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

    props: {},

    data: () => ({
      // Clothes array
      clothes: [],
      // Days Array
      days: [],
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
      // Loads Clothes.
      loadClothes(){
        // Load Clothes.
        this.clothes = this.$store.state.clothes.clothes;
      },

      // Loads Days
      loadDays(){
        // Load Days.
        this.days = this.$store.state.days.days;
        // Set worn.
        this.setWorn();
      },

      // Sets worn array.
      setWorn(){
        // Reset previous worn data.
        this.worn = [];

        // Loop through all Days.
        this.days.forEach(day => {

          // Set formated date.
          let formatedDate = day.date.slice(0,10);

          // Loop through all Clothes worn on a Day.
          day.clothes.forEach(cloth => {

            // Add to the worn array.
            this.worn.push({

              // Cloth name.
              name: cloth.title,
              // Start and end properties determine date when item was worn on.
              start: formatedDate,
              end: formatedDate
              
            });
          });
        });

      },

      // Views Day.
      viewDay ({ date, day }) {
        // Sets assigned date.
        this.currentDayInformation.date = date || day.date;

        // Itterates through all Dates.
        for(let i = 0; i < this.days.length; i++){

          // Looks for assigned Date among existing Days.
          // Slice removes additional backEndDate data.
          if(this.currentDayInformation.date === this.days[i].date.slice(0, 10)){
            // Sets Day id.
            this.currentDayInformation.id = this.days[i].id;

            // Itterates through all Clothes worn on a date.
            this.days[i].clothes.forEach(cloth => {

              // Adds cloth id to the worn array.
              this.currentDayInformation.worn.push(cloth.id);

            });
            // Stops for loop
            break;
          }
        }

        // Sets Back-End date.
        this.currentDayInformation.backEndDate = this.currentDayInformation.date+' 00:00:00';

        // Toggles modal.
        this.modal = true;
      },
      
      // Closes view Day.
      closeViewDay(){
        // Toggles modal.
        this.modal = false;
        // Resets current Day information.
        this.currentDayInformation = {
          date: '',
          timestamp: '',
          worn: [],
        };
      },

      // Handles Day save
      handleDaySave(){
        // Reload Days.
        this.loadDays();
        // Close view Day.
        this.closeViewDay();
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

    mounted() {
      // Load Clothes.
      this.loadClothes();
      // Load Days.
      this.loadDays();

      // Bus methods.
      EventBus.$on("handleDaySave", this.handleDaySave);
    },
  }
</script>
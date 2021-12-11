<template>
  <div class="text-center my-3">
    <!-- There are clothes in the DB -->
    <div v-if="clothes.length > 0">

      <ButtonCreate
        key="clothes"
        scenario="day"
        origin="DayIndex"
      />

    </div>

    <!-- There are no clothes in the DB -->
    <div v-else>

      <Message
        type="info"
        title="No Clothes found"
        body="There is no existing data, please insert a cloth to continue."
      />

      <ButtonCreate
        key="noClothes"
        scenario="cloth"
        origin="DayIndex"
      />

    </div>
  </div>
</template>

<script>

export default {

  props: { clothes: Array },

  data() {
    return {
      alert: true,
    };
  },

  methods: {
    wearClothes() {
      console.log(1);
    },
  },
};
</script>
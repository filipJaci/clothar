<template>
    <div>
        <div class="text-white text-center my-1" v-if="show">
            <v-btn
                @click="goBack"
                color="blue-grey"
                dark
                x-large
            >
                Back
            </v-btn>
        </div>
    </div>
</template>

<script>
export default {
    computed: {
        // Current route
        name: function(){
            return this.$route.name;
        },
        // Back is shown/hidden
        show: function(){
            // Current route exists in "navigate" object
            return this.navigate[this.name] !== undefined;
        }
    },
    data(){
        return{
            navigate: {
                // Navigate from (route name) - to (path)
                'clothes.create': '/clothes'
            },
        }
    },
    methods: {
        goBack(){
            // Push from[to]
            this.$router.push(this.navigate[this.name])
            // Handle errors
            // Meant to handle guard navigation error
            .catch(error => {});
        }
    },
}
</script>

<style>

</style>

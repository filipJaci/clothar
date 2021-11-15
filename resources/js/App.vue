<template>
    <div class="container">

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="collapse navbar-collapse">
                <div class="navbar-nav">
                    <router-link to="/" class="nav-item nav-link">Home</router-link>
                    <router-link to="/cloth" class="nav-item nav-link">Cloth</router-link>
                    <router-link to="/day" class="nav-item nav-link">Day</router-link>
                </div>
            </div>
        </nav>
       
        <br/>
        
        <router-view></router-view>

    </div>
</template>

<script>
    export default {}
</script>
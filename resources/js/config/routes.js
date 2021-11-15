import Vue from 'vue';


import ClothIndex from '../views/Cloth/ClothIndex.vue';
import DayIndex from '../views/Day/DayIndex.vue';
import ExampleComponent from '../views/ExampleComponent.vue';

export const routes = [
    {
        name: 'home',
        path: '/',
        component: ExampleComponent
    },
    {
        name: 'cloth',
        path: '/cloth',
        component: ClothIndex
    },
    {
        name: 'day',
        path: '/day',
        component: DayIndex
    }
];
import Cloth from './components/Cloth.vue';
import Day from './components/Day.vue';
import ExampleComponent from './components/ExampleComponent.vue';

export const routes = [
    {
        name: 'home',
        path: '/',
        component: ExampleComponent
    },
    {
        name: 'cloth',
        path: '/cloth',
        component: Cloth
    },
    {
        name: 'day',
        path: '/day',
        component: Day
    }
];
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
        name: 'clothes',
        path: '/clothes',
        component: ClothIndex
    },
    {
        name: 'days',
        path: '/days',
        component: DayIndex
    }
];
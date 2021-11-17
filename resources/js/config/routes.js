import ClothIndex from '../views/Cloth/ClothIndex.vue';
import ClothCreate from '../views/Cloth/ClothCreate.vue';

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
        name: 'clothes.create',
        path: '/clothes',
        component: ClothCreate
    },
    {
        name: 'days',
        path: '/days',
        component: DayIndex
    }
];
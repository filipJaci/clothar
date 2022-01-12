import ClothIndex from '../views/Cloth/ClothIndex.vue';
import ClothCreate from '../views/Cloth/ClothCreate.vue';
import ClothEdit from '../views/Cloth/ClothEdit.vue';

import DayIndex from '../views/Day/DayIndex.vue';
import DayCreate from '../views/Day/DayCreate.vue';

export const routes = [
    {
        name: 'home',
        path: '/',
        component: ClothIndex
    },
    {
        name: 'clothes',
        path: '/clothes',
        component: ClothIndex
    },
    {
        name: 'clothes.create',
        path: '/clothes/create',
        component: ClothCreate
    },
    {
        name: 'days',
        path: '/days',
        component: DayIndex
    },
    {
        name: 'days.create',
        path: '/days/create',
        component: DayCreate
    }
];
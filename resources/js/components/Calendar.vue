<script>
import { Link } from '@inertiajs/vue3';

export default {
    props: {
        days: Array,
        day: Object,
        timestamp: Number,
        human_date: String,
        weekdays: Array
    },
    emits: ['setDay']
}
</script>

<template>
    <div class="text-center font-semibold tracking-wider flex items-center justify-between px-4">
        <Link href="/" :data="{ timestamp, next: 0 }" class="text-[10px] cursor-pointer hover:scale-[1.1] px-2 py-1 rounded-full hover:bg-gray-100">&#10094;</Link>
        <h1 v-text="human_date"></h1>
        <Link href="/" :data="{ timestamp, next: 1 }" class="text-[10px] cursor-pointer hover:scale-[1.1] px-2 py-1 rounded-full hover:bg-gray-100">&#10095;</Link>
    </div>
    <div class="grid grid-cols-7 gap-0.5 uppercase tracking-wider text-gray-500 font-semibold">
        <template v-for="weekday in weekdays">
            <small class="text-center text-[9px]" v-text="weekday"></small>
        </template>
    </div>
    <div class="grid grid-cols-7 gap-0.5 shadow shadow-slate-500 rounded-md p-0.5">
        <template v-for="x in days" :key="x.id">
            <div class="text-center grid place-content-center font-semibold shadow-xs aspect-square rounded-md hover:bg-gray-100 cursor-pointer border-amber-500" :class="x.is_today ? 'bg-orange-300' : day?.id == x.id ? 'border-2' : x.is_focus_month ? 'bg-white' : 'bg-slate-200 opacity-50'" @click="$emit('setDay', x)">
                <h1 class="text-xs" v-text=x.day_int></h1>
                <div class="mx-auto flex items-center gap-x-0.5">
                    <span :class="{'opacity-0': !x.hasOutstanding}" class="block bg-red-500 w-1 h-1 rounded-full"></span>
                    <span :class="{'opacity-0': !x.hasCompleted}" class="block bg-green-500 w-1 h-1 rounded-full"></span>
                </div>
            </div>
        </template>
    </div>
</template>
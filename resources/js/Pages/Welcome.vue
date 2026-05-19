<script>
import { Link } from '@inertiajs/vue3';
import Calendar from '../components/Calendar.vue';
import Modal from '../components/Modal.vue';

export default {
    components: {
        Calendar, Modal
    },
    props: {
        timestamp: Number,
        human_date: String,
        days: Array
    },
    data() {
        return {
            time: '00:00',
            yay: {
                intval: null,
                show: false
            },
            weekdays: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            day: null,
            entries: {
                outstanding: [],
                completed: []
            },
            modals: {
                new: {
                    show: false,
                    header: 'New Task Entry',
                }
            }
        }
    },
    methods: {
        handleYay() {
            if (this.yay.intval) {
                clearTimeout(this.yay.intval)
                this.yay.show = false;
            }
            this.yay.show = true;
            this.yay.intval = setTimeout(() => this.yay.show = false, 7500)
        },
        setDay(day) {
            this.day = day
            this.fetchEntries()
        },
        async fetchEntries() {
            const params = new URLSearchParams({ day: this.day.day })
            const response = await fetch('/tasks?' + params)
            const json = await response.json()
            const {outstanding, completed} = json.data.entries
            this.entries.outstanding = outstanding;
            this.entries.completed = completed;
        },
        async onCreateEntrySubmit(el) {
            const form = el.target;
            const formData = new FormData(form)
            const params = new URLSearchParams({ day: this.day.day })
            const response = await fetch('/tasks?' + params, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            })
            const json = await response.json()
            if (!response.ok) {
                return;
            }
            this.modals.new.show = false;
            window.location.reload();
        },
        async mark(task) {
            await fetch('/tasks/' + task.id, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json'
                }
            })
            window.location.reload();
        },
        async trashTask(task) {
            await fetch('/tasks/' + task.id, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json'
                }
            })
            window.location.reload();
        },
        updateTime() {
            const d = new Date()
            let hours = d.getHours();
            let mins = d.getMinutes()
            if (mins < 10) mins = '0' + mins;
            if (hours < 10) hours = '0' + hours;
            this.time =`${hours}<span class="animate-pulse">:</span>${mins}`
        }
    },
    mounted() {
        this.day = this.days.find((x) => x.is_today);
        this.fetchEntries()
        setInterval(() => this.updateTime(), 1000)
    }
}
</script>

<template>
    <div class="flex justify-between items-start">
        <button class="bg-indigo-500 px-4 tracking-wide py-1 text-xs rounded cursor-pointer hover:bg-indigo-600 font-semibold shadow shadow-black text-white" @click="handleYay">Marmot</button>
        <small class="tracking-widest font-semibold text-xs" v-html="time"></small>
    </div>

    <img v-show="yay.show" src="/public/images/marmot-ai-dance.gif" class="z-10 fixed w-[225px] h-auto left-[50%] top-[50%] -translate-[50%] shadow shadow-black rounded-md" @click="yay = false" />

    <div class="w-full max-w-[315px] space-y-1.5 mx-auto">
        <Calendar :days="days" @setDay="(day) => setDay(day)" :day="day" :weekdays="weekdays" :timestamp="timestamp" :human_date="human_date"  />

        <div class="text-center">
            <Link href="/" class="text-[9px] cursor-pointer hover:underline">Back to today</Link>
        </div>
    </div>

    <div class="mt-12 max-w-[1000px] mx-auto">
        <div class="flex items-center justify-between">
            <h1 class="font-semibold" v-text="day?.human_date"></h1>
            <button @click="modals.new.show = true" class="bg-blue-500 px-4 tracking-wide py-1 text-xs rounded cursor-pointer hover:bg-blue-600 font-semibold shadow shadow-black text-white">New Entry</button>
        </div>

        <div class="mt-4 pl-2 sm:pl-6 space-y-6 max-w-[1000px]">
            <div>
                <h2 class="text-sm font-semibold tracking-wide"><span class="text-lg pr-1">&#10134;</span>Outstanding: <span v-text="entries.outstanding.length"></span></h2>
                <div class="space-y-2 max-h-[275px] overflow-scroll scrollbar-hide">
                    <template v-for="x of entries.outstanding">
                        <div class="border rounded bg-red-50 p-2 relative">
                            <button class="absolute top-1 right-1 bg-red-500 rounded text-white py-1 shadow shadow-black hover:bg-red-600 font-semibold text-xs px-4 cursor-pointer" @click="trashTask(x)">Trash</button>
                            <small v-show="x.carried" class="text-xs">&#127986;</small>
                            <h1 class="font-semibold tracking-wide text-[14px]" v-text="x.title"></h1>
                            <p class="whitespace-pre text-[11px] my-1" v-text="x.notes"></p>
                            <div class="flex items-end justify-between">
                                <small class="text-[10px]">Created: <span v-text="x.human_created_at"></span></small>
                                <button class="bg-green-500 rounded text-white py-1 shadow shadow-black hover:bg-green-600 font-semibold text-xs px-4 cursor-pointer" @click="mark(x)">Mark complete <span class="pl-2">&#10003;</span></button>
                            </div>
                        </div>
                    </template>
                    <template v-if="!entries.outstanding.length">
                        <div class="text-center">
                            <p>No entries found.</p>
                        </div>
                    </template>
                </div>
            </div>
            <div>
                <h2 class="text-sm font-semibold tracking-wide"><span class="text-lg pr-1">&#10004;</span>Completed: <span v-text="entries.completed.length"></span></h2>
                <div class="space-y-2 max-h-[225px] overflow-scroll scrollbar-hide">
                    <template v-for="x of entries.completed">
                        <div class="border rounded bg-green-100 p-2 relative">
                            <button class="absolute top-1 right-1 bg-red-500 rounded text-white py-1 shadow shadow-black hover:bg-red-600 font-semibold text-xs px-4 cursor-pointer" @click="trashTask(x)">Trash</button>
                            <h1 class="font-semibold tracking-wide text-[14px]" v-text="x.title"></h1>
                            <p class="whitespace-pre text-[11px] my-1" v-text="x.notes"></p>
                            <div class="flex items-end justify-between">
                                <small class="text-[10px]">Completed: <span v-text="x.human_completed_on"></span></small>
                                <button class="bg-indigo-500 rounded text-white py-1 shadow shadow-black hover:bg-indigo-600 font-semibold text-xs px-4 cursor-pointer" @click="mark(x)">Mark outstanding <span class="pl-2">&#9873;</span></button>
                            </div>
                        </div>
                    </template>
                    <template v-if="!entries.completed.length">
                        <div class="text-center">
                            <p>No entries found.</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <Modal :show="modals.new.show" :header="modals.new.header" @close="() => modals.new.show = false">
        <template v-slot:content>
            <div class="flex justify-end">
                <small>To be assigned: <span v-text="day?.human_date"></span></small>
            </div>
            <form class="space-y-6" @submit.prevent="onCreateEntrySubmit">
                <div class="flex flex-col text-sm">
                    <label class="font-semibold">Entry Title:</label>
                    <input type="text" name="title" class="rounded border w-1/2 p-1.5" placeholder="..." />
                </div>
                <div class="flex flex-col text-sm">
                    <label class="font-semibold">Notes:</label>
                    <textarea rows="4" name="notes" class="rounded border w-full px-1.5 py-0.5" placeholder="..."></textarea>
                </div>
                <div class="space-y-3">
                    <div class="flex gap-x-1.5 text-sm">
                        <input type="checkbox" name="completed" class="cursor-pointer w-5 h-5" />
                        <label class="font-semibold">Mark as completed</label>
                    </div>
                </div>
                <div class="w-fit ml-auto">
                    <button class="bg-blue-500 px-4 py-1 text-white tracking-wide text-xs rounded cursor-pointer hover:bg-blue-600 font-semibold shadow shadow-black">Submit</button>
                </div>
            </form>
        </template>
    </Modal>
</template>
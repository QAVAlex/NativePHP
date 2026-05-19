<script>
export default {
    props: {
        show: Boolean,
        header: String
    },
    emits: ['close']
}
</script>

<template>
    <div v-show="show" class="fixed top-0 left-0 min-h-screen h-full w-full backdrop-blur-[2px] bg-[#d3d3d390]">
        <div class="w-full h-full grid place-content-center">
            <div class="w-[575px] max-w-[575px] rounded min-h-[200px] bg-white rouunded shadow shadow-black">
                <div class="border-b p-4 flex items-center justify-between">
                    <h1 class="font-semibold text-lg" v-text="header"></h1>
                    <button @click="$emit('close')" class="cursor-pointer text-lg hover:scale-[1.1]">&#10006;</button>
                </div>
                <div class="p-4">
                   <slot name="content"></slot>
                </div>
            </div>
        </div>
    </div>
</template>
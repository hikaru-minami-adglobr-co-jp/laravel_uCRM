<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput.vue";


const emitTest = e => console.log(e)
</script>

<template>
    <GuestLayout>
        <label value="件名">タイトル</label>
        <input
        modelValue="初期値が入ります"
        @update:modelValue=""emitTest
        >

        </input>
    </GuestLayout>
</template>

<script setup>
import { Link } from "@inertiajs/inertia-vue3";

// ↓コントローラーから渡ってくるなら使う
const props = defineProps({
    blogs: Array,
});
</script>

<template>
    <!-- $page.props.flash.messageに情報が入っていたら表示する。 -->
    <div v-if="$page.props.flash.message" class="bg-blue-300">
        {{ $page.props.flash.message }}
    </div>
    <ul>
        <li v-for="blog in blogs" :key="blog.id">
            件名：
            <Link
                class="text-blue-400"
                :href="route('inertia.show', { id: blog.id })"
                >{{
                    //クリックするとidが出る
                    blog.title
                }}</Link
            >, 本文： {{ blog.content }}
        </li>
    </ul>
    aaaa
</template>

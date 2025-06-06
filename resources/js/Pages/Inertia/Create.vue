<script setup>
import { reactive } from "vue";
import { Inertia } from "@inertiajs/inertia";

defineProps({
    //[InertiaTestCon.phpから渡ってくる]
    errors: Object,
});

const form = reactive({
    title: null,
    content: null,
});

const submitFunction = () => {
    Inertia.post("/inertia", form); //postという通信メソッドで、とび先はstoreのURL(/inertia)にアクセスする必要がある。
}; //第2引数にform(送るデータ)を指定したことになる。
</script>

<template>
    <form @submit.prevent="submitFunction">
        <input type="text" name="title" v-model="form.title" /><br />
        <div v-if="errors.title">{{ errors.title }}</div>
        <input type="text" name="content" v-model="form.content" /><br />
        <div v-if="errors.content">{{ errors.content }}</div>

        <button>送信</button>
    </form>
</template>

<!-- laravel,inertia,vue.js,JS色んな機能を少しづつ使っている。 -->

<!--
| 技術             | 使っている場所                                                    | 役割                           |
| -------------- | ---------------------------------------------------------- | ---------------------------- |
| **Vue 3**      | `<script setup>`, `reactive`, `v-model`, `@submit.prevent` | フロントエンド UI、フォーム状態管理、イベント処理   |
| **Inertia.js** | `Inertia.post()`                                           | Laravel バックエンドへ SPA 的にデータを送信 |
| **Laravel**    | `/inertia` というルート                                     | 送信された POST リクエストを受け取って処理する   |
| **JavaScript** | 関数、import、オブジェクト構造                               | すべての構成の基礎                    | -->

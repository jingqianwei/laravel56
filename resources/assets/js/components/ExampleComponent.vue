<template>
    <div>
        <el-button @click="visible = true">Button</el-button>
        <el-dialog :visible.sync="visible" title="Hello world">
            <p>Try Element</p>
        </el-dialog>
        <text-content></text-content>
        <cart-content></cart-content>
    </div>
</template>

<script>
    Vue.component('text-content', require('./page/TextContent.vue'));
    Vue.component('cart-content', require('./page/CartContent.vue'));
    export default {
        //注意传的值不能为空
        data() {
            return { visible: false }
        }
    }
</script>

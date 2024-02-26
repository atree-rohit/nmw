<style scoped>
.modal {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(77, 77, 77, 0.7);
    transition: all 0.4s;
}

.modal:target {
    visibility: visible;
    opacity: 1;
}

.modal__content {
    border-radius: 4px;
    position: relative;
    width: 500px;
    max-width: 90%;
    background: #fff;
    padding: 1em 2em;
}

.modal__footer {
    text-align: right;
    a {
        color: #585858;
    }
    i {
        color: #d02d2c;
    }
}
.modal__close {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #585858;
    text-decoration: none;
}
</style>

<template>
    <div class="wrapper">
        <a class="btn btn-sm btn-outline-success" href="#demo-modal">
            {{ btn_label }}
        </a>
    </div>

    <div id="demo-modal" class="modal">
        <div class="modal__content">
            <h1>{{ btn_label }}</h1>
            <table class="table border border-secondary">
                <thead class="table-secondary">
                    <tr>
                        <th
                            v-for="col in columns"
                            :key="col.value"
                            v-text="col.label"
                        />
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in props.data" :key="row.id">
                        <td>{{ row }}</td>
                    </tr>
                </tbody>
            </table>

            <a href="#" class="modal__close">&times;</a>
        </div>
    </div>
</template>

<script lang="js" setup>

const props = defineProps({
    btn_label: {
        type: String,
        required: true
    },
    data: {
        type: Array,
        required: true
    },
    columns: {
        type:Array,
        required: true
    }
})
// console.log(1, props.data)
</script>

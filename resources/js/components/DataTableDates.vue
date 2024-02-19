<style></style>

<template>
    <table class="table table-sm border border-warning">
        <thead class="table-warning">
            <tr>
                <th>Date</th>
                <th>Observations</th>
            </tr>
        </thead>
        <tbody>
            <tr
                v-for="(observations, day) in getDatesData(dates_data)"
                :key="day"
            >
                <td v-text="day" />
                <td v-text="observations" />
            </tr>
        </tbody>
    </table>
</template>

<script lang="js" setup>
const props = defineProps({
    dates_data: {
        type: Object,
        required: true
    }
})

const getDatesData = (data) => {
    let op = {};
    Object.keys(data).forEach((date) => {
        const date_split = date.split("-07-");
        op[date_split[1]] = data[date];
    });
    return op;
};
</script>

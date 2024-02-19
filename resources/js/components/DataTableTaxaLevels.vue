<style></style>

<template>
    <table class="table table-sm border border-primary">
        <thead class="table-primary">
            <tr>
                <th>Level</th>
                <th>Taxa</th>
                <th>Observations</th>
                <!-- <th>Species List</th> -->
            </tr>
        </thead>
        <tbody>
            <tr v-for="level in taxa_levels" :key="level">
                <template v-if="taxa_levels_data.count_id_levels[level]">
                    <td>{{ level }}</td>
                    <td>
                        {{ taxa_levels_data.count_id_levels[level].total_taxa }}
                    </td>
                    <td>
                        {{
                            taxa_levels_data.count_id_levels[level]
                                .total_observations
                        }}
                    </td>
                    <!-- <td>
                        <modal
                            btn_label="Species List"
                            :data="
                                taxa_levels_data.count_id_levels[level].species
                            "
                            :columns="table_cols"
                            :key="`${year}_${level}`"
                        >
                        </modal>
                    </td> -->
                </template>
            </tr>
        </tbody>
    </table>
</template>

<script lang="js" setup>
import Modal from "./Modal.vue";
const props = defineProps({
    year: {
        type: String,
        required: true,
    },
    taxa_levels_data: {
        type: Object,
        required: true
    }
})
const table_cols = [
    {
        label: "Scientific Name",
        value: "scientific_name"
    },
    {
        label: "Common Name",
        value: "common_name"
    },
    {
        label: "Observations",
        value: "observations"
    }
]
const taxa_levels = [
    "order",
    "superfamily",
    "family",
    "subfamily",
    "tribe",
    "subtribe",
    "genus",
    "species",
    "subspecies",
    "variety",
    "form",
];

const showSpecies = (species_list) => {
    console.log(species_list);
};
</script>

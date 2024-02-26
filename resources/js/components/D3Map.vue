<style>
.switcher-sm .btn {
    font-size: 0.9rem !important;
}
#map {
    display: flex;
    justify-content: space-around;
}
.map-controls {
    display: grid;
    grid-template-rows: 1fr 1fr;
}
.map-controls .switcher {
    display: flex;
    justify-content: center;
    align-items: center;
}
.switcher {
    margin: 0.25rem;
}
.switcher .btn {
    cursor: pointer;
    border-radius: 0 0 0 0;
    border: 1px solid #aaa;
    padding: 0.25rem 1rem;
}
.switcher .btn:first-child {
    border-radius: 1rem 0 0 1rem;
}
.switcher .btn:last-child {
    border-radius: 0 1rem 1rem 0;
}
.switcher .btn.selected {
    background: green;
    color: white;
}
#map #map-stats {
    border: 1px solid pink;
    width: 50%;
    margin: 0 5px;
    max-height: 80vh;
    overflow: scroll;
}
#map-container-2023 {
    display: flex;
    justify-content: center;
}
.map-boundary path.state-boundary {
    stroke-linejoin: round;
    stroke-width: 0.5;
    stroke: rgba(0, 0, 0, 1);
    fill: none;
}
.map-boundary path:not(.state-boundary) {
    stroke-linejoin: round;
    stroke-width: 0.25;
    stroke: rgba(0, 0, 0, 0.5);
}
.map-boundary path:not(.state-boundary):hover {
    cursor: pointer;
    fill: beige;
}
.map-boundary .current-state {
    stroke: rgba(0, 50, 255, 0.75);
    stroke-width: 0.25px;
    filter: brightness(1.25);
}
.map-boundary .selected-polygon {
    /*fill: #afa;*/
    fill: #ffff55;
    stroke: rgba(255, 50, 0, 0.75);
    stroke-width: 0.5px;
}
.poly_text {
    fill: #545;
    font-size: 0.85rem;
    transition: fill 0.125s;
    text-shadow: 0px 0px 1px white, 0px 0px 2px white, 0px 0px 3px white,
        0px 0px 4px white, 0px 0px 5px white;
}
.poly_text:hover {
    fill: #00c;
    text-shadow: 0px 0px 5px #fff;
    cursor: pointer;
    font-weight: 1000;
}
.map-points circle {
    stroke-width: 0.5px;
    stroke: rgba(0, 0, 0, 0.25);
    fill: transparent;
}
.map-points circle:hover {
    cursor: pointer;
    stroke: rgba(0, 255, 0, 0.5);
}
.mapDiv svg {
    background: hsl(200, 50%, 75%);
}
.small-text {
    font-size: 0.85rem;
}
.legendCells:after {
    content: "";
    display: block;
    width: 100%;
    height: 100%;
    background-color: #ffffff;
    border: 1px solid black;
}
.selected-polygon {
    stroke: red !important;
    stroke-width: 1.5px !important;
    z-index: 100;
}
.legendCells .cell text {
    display: flex;
    align-items: center;
}
table {
    border-collapse: collapse;
}
th,
td {
    border: 1px solid white;
}
th {
    white-space: nowrap;
    border: 1px solid #555;
    cursor: pointer;
}
td {
    padding: 0.5rem 1rem;
}
tbody tr:hover td {
    background: #ffa;
}

.d3-tooltip {
    position: absolute;
    top: 0;
    /* z-index: 10;
	visibility: hidden; */
    padding: 10px;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 4px;
    color: #fff;
}
@media screen and (max-width: 800px) {
    .poly_text {
        font-size: 3.5vw;
    }
}
</style>

<template>
    <div class="mapDiv">
        <div id="map">
            <div id="containerID"></div>
        </div>
    </div>
</template>

<script lang="js" setup>
import {ref, computed, watch, onMounted } from "vue";
import { useStore } from "vuex";
import * as d3 from 'd3'
import * as d3Legend from "d3-svg-legend"

const store = useStore();

const props = defineProps({
    level: {
        type: String,
        required: true,
    },
	year: {
		type: Number,
        required: true,
	},
	data: {
		type: Array,
        required: true,
	},
	labels: {
		type: Boolean,
        required: false,
		default: true,
	}
});

const json = computed(() => store.state.geojson[props.level]);

const polygons = ref(null);
const path = ref(null);
const svg = ref({});
const projection = ref({});
const colors = ref(null);
const legend = ref({});
const state_data = ref({});
const state_max = ref(0);
const height = ref(0);
const width = ref(0);
const tooltip = ref(null);
const containerID = ref("containerIDPrefix" + Math.floor(Math.random() * 1000)); // Generate a unique container ID

watch(() => props.data, () => {
	console.log("props.data")
	init()
})

watch(() => props.labels, () => {
	console.log("props.labels")
	init();
})

watch(() => props.year, () => {
	console.log("props.year")
	init();
})

onMounted(() => {
	console.log("map mounted")
	init()
})

const zoom = () => d3.zoom()
	.scaleExtent([0.5, 10])
	.on("zoom", handleZoom)

const handleZoom = (event) => {
	const {transform} = event
	svg.value.selectAll(".map-boundary, .map-labels, .polygons")
		.attr('transform', transform)
}
const format_number = (num) => num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

const capitalizeWords = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : "";

const getMatchingPolygon = (polygon) => {
	const {level, data} = props;
	const key = level.slice(0, -1)
	return data.find((d) => d.name == polygon[key])
}

const color_polygon = (polygon) => colors.value(getMatchingPolygon(polygon)?.value ?? 0)

const init = ()  => {
	init_vars()
	init_tooltip()
	init_colors()
	renderMap()
}
const init_vars = ()  => {
	containerID.value = "map-container_" + props.year
	polygons.value = null
	path.value = null
	svg.value = {}
	height.value = window.innerHeight * 0.6
	width.value = window.innerWidth * 0.75
	projection.value = d3.geoMercator().scale(750).center([75, 22])
	path.value = d3.geoPath().projection(projection.value)
}

const init_tooltip = () => {
	const existingTooltip = d3.select('.d3-tooltip');
    if (!existingTooltip.empty()) {
        tooltip.value = existingTooltip
        return;
    }
	tooltip.value = d3.select('body')
				.append('div')
				.attr('class', 'd3-tooltip')
				.style('visibility', 'hidden')
				.text('a simple tooltip')
}
const init_legend = () => {
	legend.value = d3Legend.legendColor()
					.shapeHeight(20)
					.shapeWidth(40)
					.scale(colors.value)
					.labelFormat(d3.format(",.0f"))
					.orient('horizontal')
					.labelOffset(-10)
					.labelAlign("middle")
					.cells(5)
}

const init_colors = () => {
	const max = Math.max(...props.data.map((d) => d.value))
	colors.value = d3.scaleLinear()
		.domain([0, 1, max * 0.25, max])
		.range(["#f77", "#ca0", "#ada", "#3d3"])
		.clamp(true)
}

const renderMap = () => {
	init_legend()

	if (!d3.select(`#containerID svg.svg-content`).empty()) {
		console.log("delete")
		d3.select(`#containerID svg.svg-content`).remove()
	}
	svg.value = d3.select(`#containerID`)
				.append("svg")
					.attr("preserveAspectRatio", "xMinYMin meet")
					.attr("width", width.value)
					.attr("height", height.value)
					.classed("svg-content", true)

	let base = svg.value.append("g")
		.classed("map-boundary", true)
		.selectAll("path").append("g")

	let legendGroup = svg.value.append("g")
        .classed("map-legend", true)
        .attr("transform", "translate(" + (width.value /2 ) + ", 25)"); // Adjust the position as needed


	let base_text = svg.value.append("g")
		.classed("map-labels", true)

	polygons.value = base.append("g")
		.classed("polygons", true)

	json.value.features.forEach((polygon) => {
		drawPolygon(polygon)
		if(props.labels){
			drawPolygonLabel(base_text, polygon)
		}
	})

	svg.value.call(zoom())
	legendGroup.call(legend.value);
	// console.log(json.value)
}

const drawPolygon = (polygon) => {
	polygons.value.append("g")
		.data([polygon])
		.enter().append("path")
		.attr("d", path.value)
		.attr("fill", (d) => color_polygon(polygon.properties))
		.on('mouseover', (d, i) => {
			tooltip.value.html(hover_text(polygon.properties))
				.style('visibility', 'visible')
		})
		.on('mousemove', (event, d) => {
			tooltip.value
				.style('top', event.pageY - 10 + 'px')
				.style('left', event.pageX + 10 + 'px')
		})
		.on('mouseout', () => tooltip.value.html(``).style('visibility', 'hidden'))
}

const drawPolygonLabel = (base_text, polygon) => {
	let polygon_data = getMatchingPolygon(polygon.properties)
	let label_text = ""
	const centroid = projection.value(d3.geoCentroid(polygon));
	if(polygon_data){
		label_text = format_number(polygon_data.value)
	}


	base_text.append("text")
	.classed("poly_text", true)
        .attr("x", centroid[0])
        .attr("y", centroid[1])
        .attr("dy", "0.35em")
        .attr("text-anchor", "middle")
        .text(label_text)
		.on('mouseover', () => {
			tooltip.value.html(hover_text(polygon.properties))
				.style('visibility', 'visible');
		})
		.on('mousemove', (event, d) => {
			tooltip.value
				.style('top', event.pageY - 10 + 'px')
				.style('left', event.pageX + 10 + 'px')
		})
		.on('mouseout', () => tooltip.value.html(``).style('visibility', 'hidden'))
	// console.log(base_text, centroid)



}

const hover_text = (properties) => {
	const {level, data} = props;
	const key = level.slice(0, -1)
	let polygon_data =  data.find((d) => d.name == properties[key])

	if(!polygon_data) {
		polygon_data = {
			name: properties[key],
			value: 0
		}
	}
	return `<table><tr><td>${polygon_data.name}</td><td>${polygon_data.value}</td></tr></table>`
}
</script>

<style>
	.switcher-sm .btn{
		font-size: 0.9rem !important;
	}
	#map{
		display: flex;
		justify-content: space-around;
	}
	.map-controls{
		display: grid;
		grid-template-rows: 1fr 1fr;
	}
	.map-controls .switcher{
		display: flex;
		justify-content: center;
		align-items: center;
	}
	.switcher{
		margin: .25rem;
	}
	.switcher .btn{
		cursor: pointer;
		border-radius: 0 0 0 0;
		border: 1px solid #aaa;
		padding: .25rem 1rem;
		
	}
	.switcher .btn:first-child{
		border-radius: 1rem  0 0 1rem;
	}
	.switcher .btn:last-child{
		border-radius: 0 1rem 1rem 0;
	}
	.switcher .btn.selected{
		background: green;
		color:white;
	}
	#map #map-stats{
		border: 1px solid pink;
		width: 50%;
		margin: 0 5px;
		max-height: 80vh;
		overflow:scroll;
	}
	#map-container {
		display:flex;
		justify-content: center;
	}
	.map-boundary path.state-boundary{
		stroke-linejoin: round;
		stroke-width: 0.5;
		stroke:rgba(0, 0, 0, 1);
		fill: none;
	}
	.map-boundary path:not(.state-boundary){
		stroke-linejoin: round;
		stroke-width: .25;
		stroke:rgba(0, 0, 0, 0.5);
	}
	.map-boundary path:not(.state-boundary):hover{
		cursor: pointer;
		fill: beige;
	}
	.map-boundary .current-state{
		stroke: rgba(0,50,255,.75);
		stroke-width:.25px;
		filter: brightness(1.25)
	}
	.map-boundary .selected-polygon{
		/*fill: #afa;*/
		fill: #ffff55;
		stroke: rgba(255,50,0,.75);
		stroke-width:.5px;
	}
	.poly_text{
		fill: #545;
		font-size: 0.85rem;
		transition: fill .125s;
		text-shadow: 
		0px 0px 1px white,
		0px 0px 2px white,
		0px 0px 3px white,
		0px 0px 4px white,
		0px 0px 5px white;
	}
	.poly_text:hover{
		fill: #00c;
		text-shadow: 0px 0px 5px #fff;
		cursor: pointer;
		font-weight: 1000;
	}
    .map-points circle{
		stroke-width: .5px;
		stroke: rgba(0,0,0,.25);
		fill: transparent;
	}
	.map-points circle:hover{
		cursor:pointer;
		stroke: rgba(0,255,0,.5);
	}
	svg{
		background: hsl(200, 50%, 75%);
	}
	.small-text{
		font-size: .85rem;
	}
	.legendCells:after{
		content: "";
		display:block;
		width: 100%;
		height: 100%;
		background-color: #ffffff;
  		border: 1px solid black;
	}
	.selected-polygon{
		stroke: red !important;
		stroke-width: 1.5px !important;
		z-index: 100;
	}
	.legendCells .cell text{
		display: flex;
  		align-items: center;
	}
    table{
		border-collapse: collapse;
	}
	th,td{
        border: 1px solid white;
    }
	th{
		white-space: nowrap;
		border: 1px solid #555;
		cursor: pointer;
	}
    td {
        padding: 0.5rem 1rem;
    }
	tbody tr:hover td{
		background: #ffa;
	}
	@media screen and (max-width: 800px) {
		.poly_text{
			font-size: 3.5vw;
		}
	}
</style>

<template>
    <div ref="mapDiv">
		<div class="map-controls">
			<div class="switcher switcher-sm">
				<button
					class="btn"
					v-for="pm in polygon_modes"
					:key="pm"
					:class="{'selected': pm === polygon_mode}"
					@click="polygon_mode = pm"
					v-text="pm"
				/>
			</div>
			<div class="switcher switcher-sm">
				<button
					class="btn"
					v-for="dm in data_modes"
					:key="dm"
					:class="{'selected': dm === data_mode}"
					@click="data_mode = dm"
					v-text="dm"
				/>
			</div>
			<div class="switcher switcher-sm">
				<button
					class="btn"
					v-for="ny in nmw_years"
					:key="ny"
					:class="{'selected': selected_years.includes(ny)}"
					@click="selectYear(ny)"
					v-text="ny"
				/>
			</div>
		</div>
		<div id="map">
			<div id="map-container"></div>
			<div id="map-stats">
				<table class="table">
					<thead>
						<tr>
							<th>Name</th>
							<th @click="sortBy('observations')">Observations
								<span v-if="data_mode == 'observations'">
									<span v-if="sort_dir == 'desc'">▼</span>
									<span v-else>▲</span>
								</span>
							</th>
							<th @click="sortBy('users')">Users
								<span v-if="data_mode == 'users'">
									<span v-if="sort_dir == 'desc'">▼</span>
									<span v-else>▲</span>
								</span>
							</th>
							<th @click="sortBy('taxa')">Taxa
								<span v-if="data_mode == 'taxa'">
									<span v-if="sort_dir == 'desc'">▼</span>
									<span v-else>▲</span>
								</span>
							</th>
							<th @click="sortBy('locations')">Locations
								<span v-if="data_mode == 'locations'">
									<span v-if="sort_dir == 'desc'">▼</span>
									<span v-else>▲</span>
								</span>
							</th>
							<th @click="sortBy('nmw')">NMW
								<span v-if="data_mode == 'nmw'">
									<span v-if="sort_dir == 'desc'">▼</span>
									<span v-else>▲</span>
								</span>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(row, k) in sortedData" :key="k">
							<td v-text="row.name"/>
							<td v-text="row.observations"/>
							<td v-text="row.users"/>
							<td v-text="row.taxa"/>
							<td v-text="row.locations"/>
							<td v-text="row.nmw"/>
						</tr>						
					</tbody>
				</table>
			</div>
		</div>
	</div>
</template>

<script lang="js">
import { defineComponent } from 'vue'
import store from '../store'
import {mapState} from 'vuex'
import * as d3 from 'd3'
import * as d3Legend from "d3-svg-legend"

import DataTable from "./DataTable.vue"

export default defineComponent({
    name: "Map",
	components: {DataTable},
    data(){
        return {
			polygon_modes: ["districts", "states", "regions"],
			polygon_mode: "districts",
			data_modes: ["observations", "users", "taxa", "locations", "nmw"],
			data_mode: "observations",
			nmw_years: [2012,2013,2014,2015,2016,2017,2018,2019,2020,2021,2022],
			nmw_year:[2012,2013,2014,2015,2016,2017,2018,2019,2020,2021,2022],
			sort_dir: "desc",
			path: null,
			svg: {},
			projection: {},
			colors: {},
			legend: {},
			state_data: {},
			selected: {
				region: null,
				state: null,
				district: null,
			},
			selected_area:"All",
			max: 0,
			state_max: 0,
			height: 0,
			width: 0,
			tooltip:null,
        }
    },
	created(){
		console.clear()
		store.dispatch("initData")
		console.log("Map")
		this.init_tooltip()
	},
    mounted(){
		// this.init()
		// if(this.regional_data.length > 0){
		// }
    },
	watch:{
		polygon_data(){
			if(Object.keys(this.polygon_data.districts).length > 0){
				this.init()
			}
		},
		polygon_mode(){
			this.renderMap()
		},
		data_mode(){
			this.renderMap()
		}

	},
    computed:{
        ...mapState(["geojson", "regional_data", "polygon_data", "selected_years"]),
		mapData(){
			return this.polygon_data[this.polygon_mode]
		},
		selectedData(){
			let data = this.polygon_data[this.polygon_mode]
			let selected = Object.keys(this.selected).find((d) => this.selected[d] != null)
			let op = []
			console.log("selected", selected)

			if(selected == undefined && data != undefined){				
				Object.keys(data).forEach((key)=> {
					op.push({
						name: key,
						observations: data[key].observations,
						users: data[key].users,
						taxa: data[key].taxa,
						locations: data[key].locations,
						nmw: data[key].nmw.map(y => y-2000).join(", "),
					})
				})
			} else if(selected == "region"){
				let region = this.selected.region
				let states = this.regional_data.find((d) => d[0] == region)[1].map((d) => d[0])
				Object.keys(this.polygon_data.states).forEach((d) => {
					if(states.indexOf(d) != -1){
						op.push({
							name: d,
							observations: this.polygon_data.states[d].observations,
							users: this.polygon_data.states[d].users,
							taxa: this.polygon_data.states[d].taxa,
							locations: this.polygon_data.states[d].locations,
							nmw: this.polygon_data.states[d].nmw.map(y => y-2000).join(", "),
						})
					}
				})
			} else if(selected == "state"){
				let region = this.geojson.states.features.find((d) => d.properties.state == this.selected.state).properties.region
				let state = this.selected.state
				let districts = this.regional_data.find((d) => d[0] == region)[1].find((d) => d[0] == state)[1].map((d) => d[0])
				console.log(this.polygon_data.districts)
				Object.keys(this.polygon_data.districts).forEach((d) => {
					if(districts.indexOf(d) != -1){
						op.push({
							name: d,
							observations: this.polygon_data.districts[d].observations,
							users: this.polygon_data.districts[d].users,
							taxa: this.polygon_data.districts[d].taxa,
							locations: this.polygon_data.districts[d].locations,
							nmw: this.polygon_data.districts[d].nmw.map(y => y-2000).join(", "),
						})
					}
				})
			}
			return op
		},
		sortedData(){
			let op = this.selectedData
			if(this.data_mode != "nmw"){
				if(this.sort_dir == "desc"){
					op = op.sort((a, b) => b[this.data_mode] - a[this.data_mode])
				} else {
					op = op.sort((a, b) => a[this.data_mode] - b[this.data_mode])
				}
			} else {
				if(this.sort_dir == "desc"){
					op = op.sort((a, b) => b[this.data_mode].length - a[this.data_mode].length)
				} else {
					op = op.sort((a, b) => a[this.data_mode].length - b[this.data_mode].length)
				}
			}
			return op
		},
		zoom() {
			return d3.zoom()
				.scaleExtent([.5, 250])
				.translateExtent([[-0.5 * this.width,-0.75 * this.height],[2.5 * this.width, 2.5 * this.height]])
				.on('zoom', this.handleZoom)
		},
    },
    methods: {
		format_number(num){
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		},
        capitalizeWords(str){
			return str ? str.charAt(0).toUpperCase() + str.slice(1) : ""
		},
        color_polygon(polygon) {
			let name = ""
			if(this.polygon_mode == "districts"){
				name = polygon.district
			} else if (this.polygon_mode == "states"){
				name = polygon.state
			} else {
				name = polygon.region
			}
			if(this.mapData){
				let polygon_data = this.mapData[name]
				if(polygon_data){
					if(this.data_mode != 'nmw'){
						return this.colors(polygon_data[this.data_mode])
					} else {
						return this.colors(polygon_data[this.data_mode].length)
					}
				}
			}
            return 'hsl(200,10%, 25%)'
        },
        handleZoom(e){
			this.zoomTransform = e.transform
			let text_size = (1/e.transform.k * .8)
            this.svg.selectAll('.poly_text')
                .attr('transform', e.transform)
				.style('font-size', `${text_size}rem`)
            this.svg.selectAll('path')
                .attr('transform', e.transform)
            this.svg.selectAll('circle')
                .attr('transform', e.transform)
				.attr("r", text_size)
        },
		init () {
			this.polygons = null
			this.path = null
			this.svg = {}
			this.height = window.innerHeight * 0.8
			this.width = this.$refs.mapDiv.clientWidth * 0.5
			if(window.innerWidth < 800){
				this.projection = d3.geoMercator().scale(600).center([110, 20])
			} else {
				this.projection = d3.geoMercator().scale(1000).center([85, 28])
			}
			this.path = d3.geoPath().projection(this.projection)
			this.renderMap()
		},
		init_tooltip(){
            this.tooltip = d3.select('body')
							    .append('div')
							    .attr('class', 'd3-tooltip')
							    .style('position', 'absolute')
							    .style('top', '0')
							    .style('z-index', '10')
							    .style('visibility', 'hidden')
							    .style('padding', '10px')
							    .style('background', 'rgba(0,0,0,0.6)')
							    .style('border-radius', '4px')
							    .style('color', '#fff')
							    .text('a simple tooltip')
        },
		init_legend() {
			this.colors = {}
			this.legend = {}
			this.max = 0
			if(this.data_mode != "nmw"){
				Object.keys(this.mapData).forEach((key) => {
					this.max = Math.max(this.max, this.mapData[key][this.data_mode])
				})
			} else {
				Object.keys(this.mapData).forEach((key) => {
					this.max = Math.max(this.max, this.mapData[key][this.data_mode].length)
				})
			}
			
			this.colors = d3.scaleLinear()
				.domain([0, 1, this.max*.25, this.max])
				.range(["#f77", "#ca0", "#ada", "#3d3"])
				.clamp(true)
			this.legend = d3Legend.legendColor()
								.shapeHeight(20)
								.shapeWidth(60)
								.scale(this.colors)
								.labelFormat(d3.format(",.0f"))
								.orient('horizontal')
								.labelOffset(-10)
								.labelAlign("middle")
								.cells(6)
		},			
		renderMap () {
			this.init_legend()
			
			if (!d3.select("#map-container svg.svg-content").empty()) {
				d3.select("#map-container svg.svg-content").remove()
			}
			this.svg = d3.select("#map-container")
						.append("svg")
							.attr("preserveAspectRatio", "xMinYMin meet")
							.attr("width", this.width)
							.attr("height", this.height)
							.classed("svg-content", true)
			if(!this.zoomTransform){
				this.zoomTransform =  d3.zoomTransform(this.svg.node())
			}

			if(this.height > this.width){
				this.legend.shapeWidth(35)
				.cells(4)
			}
			let base = this.svg.append("g")
				.classed("map-boundary", true)
				.selectAll("path").append("g")
			let base_text = this.svg.append("g")
				.classed("map-labels", true)
				.selectAll("text").append("g")
			this.polygons = base.append("g")
				.classed("polygons", true)
			
			this.geojson[this.polygon_mode].features.forEach((polygon) => {
				this.drawPolygon(polygon)
				// if(this.polygon_mode != "districts"){
				// 	this.drawPolygonLabel(base_text, polygon)
				// }
			})
			if(this.polygon_mode == "districts"){
				this.geojson.states.features.forEach((polygon) => {
					this.drawPolygonBoundary(polygon)
				})
			} else if (this.polygon_mode == "states"){
				this.geojson.regions.features.forEach((polygon) => {
					this.drawPolygonBoundary(polygon)
				})
			}
			
			this.svg.append("g")
				.attr("class", "legend")
				.attr("transform", "translate("+this.width*.45+", 25)")
				.call(this.legend)
			this.svg.call(this.zoom)

			this.svg.call(this.zoom.transform, this.zoomTransform)
		},
		drawPolygonBoundary(polygon){
			this.polygons.append("g")
				.data([polygon])
				.enter().append("path")
				.classed("state-boundary", true)
				.classed("selected-polygon", (d) => {
					return ((this.selected.state && d.properties.state == this.selected.state) 
					|| (this.selected.region && d.properties.region == this.selected.region))
				})
				.attr("properties", (d) => JSON.stringify(d.properties))
				.attr("d", this.path)
				.attr("id", this.getPolygonId(polygon.properties))
		},
		drawPolygon(polygon){
			this.polygons.append("g")
				.data([polygon])
				.enter().append("path")
				.attr("d", this.path)
				.attr("id", this.getPolygonId(polygon.properties))
				.attr("fill", (d) => this.color_polygon(polygon.properties))
				.on('mouseover', (d, i) => {
					this.tooltip.html(this.hover_text(polygon.properties))
						.style('visibility', 'visible')
				})
				.on('mousemove', (event, d) => {
					this.tooltip
						.style('top', event.pageY - 10 + 'px')
						.style('left', event.pageX + 10 + 'px')
				})
				.on('mouseout', () => this.tooltip.html(``).style('visibility', 'hidden'))
				.on("click", (d, polygon_details) => this.clicked(polygon_details))
		},
		drawPolygonLabel(base_text, polygon){
			let polygon_data = this.polygon_data[this.polygon_mode]
			let data = ""
			// if(Object.keys(polygon.properties) == 2){
			// 	polygon_data = polygon_data[polygon.properties.state]
			// } else {
			// 	polygon_data = polygon_data[polygon.properties.region]
			// }
			
			if(polygon_data){
				data = this.format_number(polygon_data[polygon.properties.state][this.data_mode])
			}
			
			base_text.append("g")
				.data([polygon])
				.enter().append("text")
				.classed("poly_text", true)
				.attr("x", (h) => this.path.centroid(h)[0] )
				.attr("y", (h) => this.path.centroid(h)[1] )
				.classed("small-text", true)
				.attr("text-anchor", "middle")
				.text(data)
				.on('mouseover', () => {
					this.tooltip.html(this.hover_text(polygon.properties))
						.style('visibility', 'visible');
				})
				.on('mousemove', (event, d) => {
					this.tooltip
						.style('top', event.pageY - 10 + 'px')
						.style('left', event.pageX + 10 + 'px')
				})
				.on('mouseout', () => this.tooltip.html(``).style('visibility', 'hidden'))
				.on("click", (d, polygon_details) => this.clicked(polygon_details))
			
			
		},
		hover_text(properties){
			const {region, state, district} = properties || {}
			let polygon_data = {}
			if(this.polygon_mode == "districts"){
				polygon_data = this.polygon_data[this.polygon_mode][district]
			} else if (this.polygon_mode == "states"){
				polygon_data = this.polygon_data[this.polygon_mode][state]
			} else {
				polygon_data = this.polygon_data[this.polygon_mode][region]
			}
			const {observations, users, taxa, locations, nmw} = polygon_data || {}
			const tooltip_data = { region, state, district, observations, users, taxa, locations, nmw}
			if(this.polygon_mode !== "districts"){
				delete tooltip_data.district
			}
			if(this.polygon_mode === "regions"){
				delete tooltip_data.state
			}
			let op = Object.entries(tooltip_data)
				.map(([key, value]) => `<tr><td>${this.capitalizeWords(key)}</td><td>${value ? value: "-"}</td></tr>`)	
			return `<table>${op.join('\n')}</table>`
			
		},
		getPolygonId(polygon){
			let op = polygon.region
			let replace_chars = [" ", "&", "(", ")", "."]
			if(polygon.state != undefined){
				op = polygon.state
			}
			if(polygon.district != undefined){
				op = polygon.district
			}
			replace_chars.forEach((c) => {
				op = op.replaceAll(c, "_")
			})
			return op
		},
		set_state_class (state, district) {
			this.mapLayers[2].features.map((p) => {
				if(p.properties.state == state && p.properties.district != district){
					d3.select("#" + this.getPolygonId(p.properties)).classed("current-state", true)
				}
			})
		},
		clicked(polygon_details) {
			const {region, state, district}	= polygon_details.properties || {}
			if(state){
				this.geojson.states.features.find((p) => {
					if(p.properties.state == polygon_details.properties.state){
						this.clicked_state(p)
					}
				})
			} else {
				this.clicked_region(polygon_details)
			}
		},
		clicked_region(polygon_details){
			const region = polygon_details.properties.region
			const polygon = polygon_details.geometry
			this.tooltip.html(``).style('visibility', 'hidden')
			let [[x0, y0], [x1, y1]] = [[0,0], [0,0]]
			
			d3.selectAll(".current-state").classed("current-state", false)
			d3.selectAll(".selected-polygon").classed("selected-polygon", false)
			if(this.selected.region == null || this.selected.region != region){
				
				this.selected.region = region;
				[[x0, y0], [x1, y1]] = this.path.bounds(polygon);
				d3.select("#" + this.getPolygonId(polygon_details.properties)).classed("selected-polygon", true)
			} else {
				this.selected = {
					district: null,
					state: null,
					region: null
				};
				[[x0, y0], [x1, y1]] = this.path.bounds(this.geojson.regions);
			}
			// this.selectArea()
			
			this.svg.transition().duration(750).call(
				this.zoom.transform,
				d3.zoomIdentity
				.translate(this.width / 2, this.height / 2)
				.scale(Math.min(8, 0.9 / Math.max((x1 - x0) / this.width, (y1 - y0) / this.height)))
				.translate(-(x0 + x1) / 2, -(y0 + y1) / 2),
			)

		},
		clicked_state(polygon_details){
			const {region, state, district} = polygon_details.properties || {}
			const polygon = polygon_details.geometry
			this.tooltip.html(``).style('visibility', 'hidden')
			let [[x0, y0], [x1, y1]] = [[0,0], [0,0]]
			
			d3.selectAll(".current-state").classed("current-state", false)
			d3.selectAll(".selected-polygon").classed("selected-polygon", false)
			if(this.selected.state == null || this.selected.state != state){
				this.selected.state = state;
				[[x0, y0], [x1, y1]] = this.path.bounds(polygon);
				d3.select("#" + this.getPolygonId(polygon_details.properties)).classed("selected-polygon", true)
			} else {
				this.selected = {
					district: null,
					state: null,
					region: null
				};
				[[x0, y0], [x1, y1]] = this.path.bounds(this.geojson.regions);
			}
			// this.selectArea()
			
			this.svg.transition().duration(750).call(
				this.zoom.transform,
				d3.zoomIdentity
				.translate(this.width / 2, this.height / 2)
				.scale(Math.min(8, 0.9 / Math.max((x1 - x0) / this.width, (y1 - y0) / this.height)))
				.translate(-(x0 + x1) / 2, -(y0 + y1) / 2),
			)
		},
		dispatchSelectArea(type, name){
			store.dispatch('setSelected', {
				filter: type,
				value: name
			})
		},
		selectArea () {
			if(this.selected_area == "All"){
				this.dispatchSelectArea("state", "All") 
			} else if(this.area_names.state.indexOf(this.selected_area) != -1){
				this.dispatchSelectArea("state", this.selected_area) 
			}
		},
		sortBy(col){
			if(this.data_mode == col){
				if(this.sort_dir == "desc"){
					this.sort_dir = "asc"
				} else {
					this.sort_dir = "desc"
				}
			} else {
				this.sort_dir = "desc"
			}
			this.data_mode = col
		},
		selectYear(year){
			store.dispatch('setYear', year)
		}
    }
})
</script>
<style scoped>
	.slug-widget{
		display: flex;
		justify-content: flex-start;
		align-items: center;	
	}
	.wrapper{
		margin-left: 8px;
		margin-top: 10px;
	}
	.slug {
		background-color: #fdfd96;
		padding: 3px 5px;
	}
	.input {
		width: auto;
	}
</style>

<template>
	<div class="slug-widget">
		<div class="icon-wrapper wrapper">
			<i class="fas fa-link"></i>
		</div>
	
		<div class="url-wrapper wrapper">
			<span class="root-url"			>{{ url }}</span>
			<span class="subdirectory-url"	>{{ subdirectory }}</span>
			<span class="slug" v-show="slug && !isEditing">{{ slug }}</span>
			<input type="text" name="slug-edit" class="input" v-show="isEditing" v-model="customSlug" />
			<input type="text" name="slug" v-model="slug" hidden/>
		</div>
		
		<div class="button-wrapper wrapper">		
			<button class="btn btn-sm btn-outline-dark" 	v-show="!isEditing" @click.prevent="editSlug">Edit</button>
			<button class="btn btn-sm btn-outline-success" 	v-show="isEditing"	@click.prevent="saveSlug">Save</button>
			<button class="btn btn-sm btn-outline-danger" 	v-show="isEditing"	@click.prevent="resetSlug">Reset</button>
		</div>
	</div>
</template>

<script>
    export default {
    	props: {
    		url: {
    			type: String,
    			required: true
    		},
 			subdirectory: {
 				type: String,
    			required: true	
    		},
    		title: {
    			type: String,
    			required: true
    		},
    	},
    	data: function() {
    		return {
    			slug: this.convertTitle(),
    			isEditing: false,
    			customSlug: '',
    			wasEdited: false	
    		}
    	},
    	methods: {
    		convertTitle: function() {
    			return Slug(this.title)
    		},
    		editSlug: function() {
    			this.customSlug = this.slug;
    			this.isEditing = true;
    		},
       		saveSlug: function() {
       			if (this.customSlug !== this.slug) this.wasEdited = true; 
       			this.slug = Slug(this.customSlug);
	 			this.isEditing = false;
    		},
       		resetSlug: function() {
       			this.slug = this.convertTitle();
       			this.wasEdited = false;
       			this.isEditing = false;
    		},

    	},
    	watch: {
    		title: _.debounce(function() {
    			if (this.wasEdited === false) this.slug = this.convertTitle()
       		},250),
    		slug: function(val) {
    			this.$emit('slug-changed',val)
    		}
    	},
    }
</script>    	

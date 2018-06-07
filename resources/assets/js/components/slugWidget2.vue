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
			<button class="btn btn-sm btn-outline-success" 	v-show="isEditing"	@click.prevent="saveSlug">{{ button }}</button>
			<button class="btn btn-sm btn-outline-danger" 	v-show="isEditing"	@click.prevent="resetSlug">Reset</button>
		</div>
	</div>
</template>

<script>
    var axios = require("axios");

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
    			slug: this.setSlug(this, this.title),
                isEditing: false,
    			customSlug: '',
    			wasEdited: false,
                api_token: this.$root.api_token,
                post_id: this.$root.post_id,
                button: 'Cancel',
            }
    	},
    	methods: {
       		editSlug: function() {
    			this.customSlug = this.slug;
                this.$emit('edit', this.slug);
    			this.isEditing = true;
    		},
       		saveSlug: function() {
       			if (this.customSlug !== this.slug) this.wasEdited = true; 
       			this.setSlug(this, this.customSlug);
                this.$emit('save', this.slug);
	 			this.isEditing = false;
    		},
       		resetSlug: function() {
       			this.setSlug(this, this.title);
                this.$emit('reset', this.slug);
       			this.wasEdited = false;
       			this.isEditing = false;
    		},
            setSlug: function(e, val) {
                let slug = Slug(val);
                let vm = this;
                if (this.api_token && slug) {    
                    axios.get('/api/posts/unique', {
                        params: {
                            api_token: vm.api_token,
                            slug: slug,
                            id: e.post_id
                        }
                    }).then(function (response) {
                        if (response.data) {
                            vm.slug = response.data;
                            vm.$emit('slug-changed', vm.slug);
                        }
                    }).catch(function (error) {
                        //console.log(error);
                        // We had an API comms error so just let the original slug through
                        // If not unique it will be caught in the controller 
                        e.slug = slug;
                    });
                } else {
                    e.slug = slug;
                }
            }
    	},
        created: function() {
            this.resetSlug();
        }
       	watch: {
    		title: _.debounce(function() {
    			if (this.wasEdited == false) this.setSlug(this, this.title);
       		}, 300),
            customSlug: _.debounce(function() {
                if (this.slug == undefined) this.slug = '';
                if (this.customSlug == this.slug) this.button='Cancel';
                else this.button='Save';
            }, 100)
    	}
    }
</script>    	

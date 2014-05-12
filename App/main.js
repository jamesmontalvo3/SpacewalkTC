(function($){

	// var AppView = Backbone.View.extend({
	// 	// el - stands for element. Every view has a element associate in with HTML 
	// 	//      content will be rendered.
	// 	el: '#container',
	// 	// It's the first function called when this view it's instantiated.
	// 	initialize: function(){
	// 		this.render();
	// 	},
	// 	// $el - it's a cached jQuery object (el), in which you can use jQuery functions 
	// 	//       to push content. Like the Hello World in this case.
	// 	render: function(){
	// 		this.$el.html("Hello World");
	// 	}
	// });
	// var appView = new AppView();



	var Event = Backbone.Model.extend({
		defaults : {
			name : "US EVA TBD"
		},
		url : "api.php/event"
	});

	var EventList = Backbone.Collection.extend({
		model : Event,
		url : "api.php/event",
		
		parse : function (response) {
			console.log(response);
			return response;
		}
	});

	var EventListViewItem = Backbone.View.extend({
		tagName : 'li',
		events : {
			'click a.event-name' : 'loadEvent'
		},

		template : _.template( $('#this-is-a-test').html(), null, { variable : 'event' } ),

		initialize : function () {
			// console.log("EventListViewItem.initialize()");
			//this.render();
		    
		    // Ensure our methods keep the `this` reference to the view itself
			_.bindAll(this, 'render', 'loadEvent');

		    // If the model changes we need to re-render
		    this.model.bind('change', this.render);


		},

		render : function () {
			// console.log("EventListViewItem.render()");

			this.$el.html(this.template(this.model.attributes));
			return this;
		},

		loadEvent : function () {
			alert(this.model.get('name'));
			// new EventView();
		}

	});

	var EventListView = Backbone.View.extend({
		el : '#container',
		collection : null,

		initialize : function () {
			console.log("  EventListView.intialize()");
		    _.bindAll(this, 'render');

			this.collection = new EventList();
		    // this.collection.bind('reset', this.render);
		    // this.collection.bind('remove', this.render);

		    /* @blog: use success callback to bind collection events after you've got the data
				.initialize() was:

				initialize : function () {
				    _.bindAll(this, 'render');
					this.collection = new EventList();
				    this.collection.bind('add', this.render);
					this.collection.fetch();
				}
				
		     */
		    var self = this;
			this.collection.fetch({
				success : function() {
					self.collection.bind('add', self.render);
					self.render();
				},
				data : { revision : "latest" }
			});
		    
		},

		render : function() {
			
			var container = document.createDocumentFragment();

			// console.log("  EventListView.render()");
			this.collection.each(function(eventModel) {
				// console.log(eventModel);
				eventListViewItem = new EventListViewItem({ model: eventModel });
				container.appendChild(eventListViewItem.render().el);
			});

			// @blog: .html() not .append()
			this.$el.html(container);

			return this;
		}

	});

	var eventListView = new EventListView();


	var EventView = Backbone.View.extend({
		el : '#container',

		initialize : function () {

		},

		render : function () {

		}

	});
})(jQuery);
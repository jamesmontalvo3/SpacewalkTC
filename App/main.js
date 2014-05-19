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
		url : "api.php/event",
		constructor : function () {
			// console.log("constructor");
			// console.log(arguments);
			Backbone.Model.apply(this, arguments);
			this.revisions = new RevisionList(arguments[0].revisions)
		},
		getEventDate : function() {
			console.log( this.revisions.last() );
			return (this.revisions.length > 0) ? this.revisions.last().get('gmt_date') : "";
		}
	});

	var Revision = Backbone.Model.extend({
		defaults : {
			event_id : null,
			gmt_date : "",
			jedi : "XX-XXXX",
			overview : "",
			revision_ts : "now",
			user_id : 1,
			items_json : "[]"
		}
	});

	var RevisionList = Backbone.Collection.extend({
		model : Revision,
		comparator : "id" // @todo: @fixme: this should compare timestamps, probably...
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

		template : _.template( $('#View-EventListViewItem').html(), null, { variable : 'event' } ),

		initialize : function () {
			// console.log("EventListViewItem.initialize()");
			//this.render();
		    
		    // Ensure our methods keep the `this` reference to the view itself
			_.bindAll(this, 'render', 'loadEvent');

		    // If the model changes we need to re-render
		    this.model.bind('change', this.render);


		},

		render : function () {
			var viewModel = this.model.attributes;
			viewModel.revision = {
				date : this.model.getEventDate()
			};

			this.$el.html(this.template( viewModel ));
			return this;
		},

		loadEvent : function () {
			new EventEditView({ model : this.model });
			// new EventView({ model : this.model });
		}

	});

	var EventListView = Backbone.View.extend({
		el : '#container',
		collection : null,

		initialize : function () {
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

		template : _.template( $('#View-EventView').html(), null, { variable : 'event' } ),

		initialize : function () {
		    _.bindAll(this, 'render');

		   this.render();
		},

		render : function () {
			var viewModel = this.model.attributes;
			viewModel.revision = this.model.revisions.last().attributes;

			this.$el.html(this.template( viewModel ));
			return this;
		}

	});

	var EventEditView = Backbone.View.extend({
		el : '#container',

		template : _.template( $('#View-EventEditView').html(), null, { variable : 'event' } ),

		events : {
			"change .simple-input" : fieldChanged,
			"change .gmt-date" : gmtFieldChanged
		},

		initialize : function () {
		    _.bindAll(this, 'render');

		   this.render();
		},

		fieldChanged : function (e) {
			var field = $(e.currentTarget),
			    data = {};
			data[field.attr('id')] = field.val();
			this.model.set(data);
		},

		gmtFieldChanged : function (e) {
			
			
			this.model.set(data);
		}

		render : function () {
			var viewModel = this.model.attributes;
			viewModel.revision = this.model.revisions.last().attributes;
			var dateInfo = viewModel.revision.gmt_date.split("/");
			console.log(viewModel.revision);
			viewModel.revision.year = dateInfo[0];
			viewModel.revision.day = dateInfo[1];

			this.$el.html(this.template( viewModel ));
			return this;
		}

	});
})(jQuery);
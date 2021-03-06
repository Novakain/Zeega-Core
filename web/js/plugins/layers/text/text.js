(function(Layer){

	Layer.Text = Layer.Model.extend({

		layerType : 'Text',
		displayCitation : false,
		linkable : true,
		visual : true,
		
		defaultAttributes: {
			'title' :'Text Layer',
			'content' : 'Text',
			'left' :30,
			'top' :40,
			'color' : '#ffffff',
			'opacity' : 1,
			'fontSize' : 500,
			'overflow' : 'visible',
			'width' : 25,
			//'height' : 10,
			
			linkable : true
		},
		
		updateContentInPlace : function()
		{
			this.visual.updateContentInPlace();
		},

		preload : function( target )
		{
			
		},
	
		play : function( z )
		{
			
		},

		stash : function()
		{
			this.display.css({'top':"-1000%",'left':"-1000%"});
		}
	
		
	});
	
	Layer.Views.Controls.Text = Layer.Views.Controls.extend({
		
		render : function()
		{
			
			var color = new Layer.Views.Lib.ColorPicker({
				property : 'color',
				color : this.model.get('attr').color,
				model: this.model,
				label : '',
				opacity : true
			});
			
			/*
			var sizeSlider = new Layer.Views.Lib.Slider({
				property : 'fontSize',
				model: this.model,
				label : 'Font Size',
				suffix : '%',
				min : 100,
				max : 1000,
				
			});
			*/
			
			var textStyles = new Layer.Views.Lib.TextStyles({
				model : this.model
			})
			
			var fontChooser = new Layer.Views.Lib.FontChooser({
				model : this.model
			})
			
			this.controls
				.append( textStyles.getControl() )
				.append( fontChooser.getControl() )
				.append( color.getControl() );
			
			return this;
		}
		
	});

	Layer.Views.Visual.Text = Layer.Views.Visual.extend({
		
		draggable : true,
		
		render : function()
		{
			
			// this should be removed later!
			var c = '';
			var b = '';
			if( _.isObject( this.model.get('attr').color ) )
			{
				var a = this.model.get('attr').color;
				c = rgbToHex(a.r,a.g,a.b);
			}
			else c = this.model.get('attr').color;
			
			if( _.isObject( this.model.get('attr').backgroundColor ) )
			{
				var a = this.model.get('attr').backgroundColor;
				b = rgbToHex(a.r,a.g,a.b);
			}
			else b = this.model.get('attr').backgroundColor;
			
			
			var style = {
				'color' : 'rgba('+ c.toRGB() +','+ (this.model.get('attr').colorOpacity || 1) +')',
				'opacity' : this.model.get('attr').opacity,
				'fontSize' : this.model.get('attr').fontSize < 100 ? '200%' : this.model.get('attr').fontSize +'%', // enforces minimum. remove this later
				'width' : this.model.get('attr').width+'%',
				'overflow' : 'visible',
				'line-height' : '100%',

				'max-height' : '-webkit-calc( '+ (100 - this.model.get('attr').top) +'% - 1px )',
				'word-wrap': 'break-word'
				
			}

			this.$el.html( _.template( this.getTemplate(), _.extend(this.model.get('attr'), {contentEditable:!this.model.player} ) ) ).css( style );


			var _this = this;
			_.delay(function(){
				_this.$('#zedit-target').css({
				'height': _this.$el.height() +'px'
			})
			},1000)

			if(!this.model.player) $(this.el).addClass('text-non-editing');
			
			this.model.trigger('ready',this.model.id)

			this.model.on('update', this.onUpdate, this);
			
			return this;
		},
		
		onUpdate : function()
		{
			var height = (100 - this.model.get('attr').top)/100 * $('#zeega-frame-workspace').height() - 1;

			this.$el.css({
				'max-height' : height +'px',
			})
			
			this.$('#zedit-target').css({
				'height': height +'px',
			})

		},

		onPlay : function()
		{
			//adjusts the height in the player
			this.$('#zedit-target').css({
				'height': this.$el.height() +'px'
			})
		},

		onLayerEnter : function()
		{
			var _this = this;
			
			this.$('#zedit-target').keyup(function(e){
				if(e.which == 27){ $(this).blur() }
				
				_this.lazySave();
			})
			.bind('paste', function(e){
				_this.$('#zedit-target').html( _this.$('#zedit-target').text() );
				_this.lazySave();
			});

			
			this.$el.click(function(){
				_this.$el.find('#zedit-target').focus();
				_this.$el.draggable('option','disabled', true);
				_this.$el.addClass('text-editing').removeClass('text-non-editing');
				
				
				
			}).focusout(function(){
				_this.$el.draggable('option','disabled', false);
				_this.$el.removeClass('text-editing').addClass('text-non-editing');
				_this.lazySave();
			})
			
			this.$el.resizable({
				stop : function(e,ui)
				{
					$(this).css('height','');
					_this.model.update({
						'width' : $(this).width() / $(this).parent().width() * 100,
					})
				}
			}).css({
				'outline' : '1px dashed red'

			});
			
			
		},
		
		lazySave : _.debounce( function(){
			
			var str = this.$el.find('#zedit-target').html();
			
			var clean = this.cleanString( str );
			
			this.model.update({
				'content' : str,
				'title' : clean
			});
		}, 1000),
		
		cleanString : function(str)
		{
			return str.replace(/(<([^>]+)>)/ig, "");
		},
		
		updateContentInPlace : function()
		{
			$(this.el).find('#zedit-target').html(this.model.get('attr').content );
		},
		
		getTemplate : function()
		{
			var html = 
			
					'<div id="zedit-target" class="inner" contenteditable="<%= contentEditable %>" ><%= content %></div>';
			
			return html;
		}
		
	});
	
})(zeega.module("layer"));




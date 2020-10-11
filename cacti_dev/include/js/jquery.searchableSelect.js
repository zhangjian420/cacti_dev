// Author: David Qin
// E-mail: david@hereapp.cn
// Date: 2014-11-05

(function($){

  // a case insensitive jQuery :contains selector
  $.expr[":"].searchableSelectContains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
      return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
  });

  $.searchableSelect = function(element, options) {
    this.element = element;
    this.options = options || {};
    this.init();

    var _this = this;

    this.searchableElement.click(function(event){
      // event.stopPropagation();
      _this.show();
    }).on('keydown', function(event){
      if (event.which === 13 || event.which === 40 || event.which == 38){
        event.preventDefault();
        _this.show();
      }
    });

    $(document).on('click', null, function(event){
      if(_this.searchableElement.has($(event.target)).length === 0)
        _this.hide();
    });

    this.input.on('keydown', function(event){
      event.stopPropagation();
      if(event.which === 13){         //enter
        event.preventDefault();
        _this.selectCurrentHoverItem();
        _this.hide();
      } else if (event.which == 27) { //ese
        _this.hide();
      } else if (event.which == 40) { //down
        _this.hoverNextItem();
      } else if (event.which == 38) { //up
        _this.hoverPreviousItem();
      }
    }).on('keyup', function(event){
      if(event.which != 13 && event.which != 27 && event.which != 38 && event.which != 40)
        _this.filter();
    })
  }

  var $sS = $.searchableSelect;

  $sS.fn = $sS.prototype = {
    version: '0.0.1'
  };

  $sS.fn.extend = $sS.extend = $.extend;

  $sS.fn.extend({
    init: function(){
      var _this = this;
      this.element.hide();

      this.searchableElement = $('<div tabindex="0" class="searchable-select"></div>');
      this.holder = $('<div class="searchable-select-holder" style="width:'+(this.options.width?this.options.width+"px":"auto")+'"></div>');
      this.dropdown = $('<div class="searchable-select-dropdown searchable-select-hide" style="width:'+(this.options.width?(parseInt(this.options.width)+20)+"px":"auto")+'"></div>');
      this.input = $('<input type="text" class="searchable-select-input" style="display:'+(this.options.show_srch?"block":"none")+'"/>');
      this.items = $('<div class="searchable-select-items"></div>');
      this.item_text = $('<div class="searchable-select-text" style="width:'+(this.options.width?(parseInt(this.options.width)-20)+"px":"auto")+'"></div>');
      this.caret = $('<div class="searchable-select-caret"></div>');

      this.scrollPart = $('<div class="searchable-scroll"></div>');
      this.hasPrivious = $('<div class="searchable-has-privious">...</div>');
      this.hasNext = $('<div class="searchable-has-next">...</div>');

      this.hasNext.on('mouseenter', function(){
        _this.hasNextTimer = null;

        var f = function(){
          var scrollTop = _this.items.scrollTop();
          _this.items.scrollTop(scrollTop + 20);
          _this.hasNextTimer = setTimeout(f, 50);
        }

        f();
      }).on('mouseleave', function(event) {
        clearTimeout(_this.hasNextTimer);
      });

      this.hasPrivious.on('mouseenter', function(){
        _this.hasPriviousTimer = null;

        var f = function(){
          var scrollTop = _this.items.scrollTop();
          _this.items.scrollTop(scrollTop - 20);
          _this.hasPriviousTimer = setTimeout(f, 50);
        }

        f();
      }).on('mouseleave', function(event) {
        clearTimeout(_this.hasPriviousTimer);
      });

      this.dropdown.append(this.input);
      this.dropdown.append(this.scrollPart);

      this.scrollPart.append(this.hasPrivious);
      this.scrollPart.append(this.items);
      this.scrollPart.append(this.hasNext);
      
      this.holder.append(this.item_text);
      this.holder.append(this.caret);

      //this.searchableElement.append(this.caret);
      this.searchableElement.append(this.holder);
      this.searchableElement.append(this.dropdown);
      this.element.after(this.searchableElement);

      this.buildItems();
      this.setPriviousAndNextVisibility();
    },

    filter: function(){
      var text = this.input.val();
      this.items.find('.searchable-select-item').addClass('searchable-select-hide');
      this.items.find('.searchable-select-item:searchableSelectContains('+text+')').removeClass('searchable-select-hide');
      if(this.currentSelectedItem.hasClass('searchable-select-hide') && this.items.find('.searchable-select-item:not(.searchable-select-hide)').length > 0){
        this.hoverFirstNotHideItem();
      }

      this.setPriviousAndNextVisibility();
    },

    hoverFirstNotHideItem: function(){
      this.hoverItem(this.items.find('.searchable-select-item:not(.searchable-select-hide)').first());
    },

    selectCurrentHoverItem: function(){
      if(!this.currentHoverItem.hasClass('searchable-select-hide'))
        this.selectItem(this.currentHoverItem);
    },

    hoverPreviousItem: function(){
      if(!this.hasCurrentHoverItem())
        this.hoverFirstNotHideItem();
      else{
        var prevItem = this.currentHoverItem.prevAll('.searchable-select-item:not(.searchable-select-hide):first')
        if(prevItem.length > 0)
          this.hoverItem(prevItem);
      }
    },

    hoverNextItem: function(){
      if(!this.hasCurrentHoverItem())
        this.hoverFirstNotHideItem();
      else{
        var nextItem = this.currentHoverItem.nextAll('.searchable-select-item:not(.searchable-select-hide):first')
        if(nextItem.length > 0)
          this.hoverItem(nextItem);
      }
    },
    clear:function(){
    	var _this = this;
    	_this.items.empty();
    	_this.element.empty();
    },
    disabled:function(){
    	this.dropdown.hide();
    	this.caret.hide();
    },
    appendItems:function(options){
    	var _this = this;
    	$.each(options,function(i,option){
    		//var item = $('<div class="searchable-select-item" data-value="'+$(this).attr('value')+'">'+$(this).text()+'</div>');
    		var item = $('<div class="searchable-select-item" data-value="'+option.value+'">'+option.text+'</div>');

            if(option.selected){
              _this.selectItem(item);
              _this.hoverItem(item);
              _this.element.append("<option value='"+option.value+"' selected>"+option.text+"</option>");
            }else{
            	_this.element.append("<option value='"+option.value+"'>"+option.text+"</option>");
            }

            item.on('mouseenter', function(){
              $(this).addClass('hover');
            }).on('mouseleave', function(){
              $(this).removeClass('hover');
            }).click(function(event){
              event.stopPropagation();
              _this.selectItem($(this));
              _this.hide();
              if(_this.options.afterItemClick){
            	  _this.options.afterItemClick.apply(_this);
                }
            });

            _this.items.append(item);
    	});
    	this.items.on('scroll', function(){
            _this.setPriviousAndNextVisibility();
          })
    },

    buildItems: function(){
      var _this = this;
      this.element.find('option').each(function(){
    	  _this.appendItems([{
    		  value:$(this).attr('value'),
    		  text:$(this).text(),
    		  selected:this.selected
    	  }]);
      });
      if(this.options.onBuildItemSuccess){
          this.options.onBuildItemSuccess.apply(this);
        }
    },
    show: function(){
      this.dropdown.removeClass('searchable-select-hide');
      this.input.focus();
      this.status = 'show';
      this.setPriviousAndNextVisibility();
      if(this.options.afterShow){
          this.options.afterShow.apply(this);
        }
    },

    hide: function(){
      if(!(this.status === 'show'))
        return;

      if(this.items.find(':not(.searchable-select-hide)').length === 0)
          this.input.val('');
      this.dropdown.addClass('searchable-select-hide');
      this.searchableElement.trigger('focus');
      this.status = 'hide';
    },

    hasCurrentSelectedItem: function(){
      return this.currentSelectedItem && this.currentSelectedItem.length > 0;
    },

    selectItem: function(item){
      if(this.hasCurrentSelectedItem())
        this.currentSelectedItem.removeClass('selected');

      this.currentSelectedItem = item;
      item.addClass('selected');

      this.hoverItem(item);

      this.item_text.text(item.text());
      this.item_text.attr("title",item.text());
      var value = item.data('value');
      this.holder.data('value', value);
      this.element.val(value);
      if(this.options.afterSelectItem){
        this.options.afterSelectItem.apply(this);
      }
    },
    hasCurrentHoverItem: function(){
      return this.currentHoverItem && this.currentHoverItem.length > 0;
    },

    hoverItem: function(item){
      if(this.hasCurrentHoverItem())
        this.currentHoverItem.removeClass('hover');

      if(item.outerHeight() + item.position().top > this.items.height())
        this.items.scrollTop(this.items.scrollTop() + item.outerHeight() + item.position().top - this.items.height());
      else if(item.position().top < 0)
        this.items.scrollTop(this.items.scrollTop() + item.position().top);

      this.currentHoverItem = item;
      item.addClass('hover');
    },

    setPriviousAndNextVisibility: function(){
      if(this.items.scrollTop() === 0){
        this.hasPrivious.addClass('searchable-select-hide');
        this.scrollPart.removeClass('has-privious');
      } else {
        this.hasPrivious.removeClass('searchable-select-hide');
        this.scrollPart.addClass('has-privious');
      }

      if(this.items.scrollTop() + this.items.innerHeight() >= this.items[0].scrollHeight){
        this.hasNext.addClass('searchable-select-hide');
        this.scrollPart.removeClass('has-next');
      } else {
        this.hasNext.removeClass('searchable-select-hide');
        this.scrollPart.addClass('has-next');
      }
    }
  });

  $.fn.searchableSelect = function(options){
	  this.each(function(){
		 var sS = new $sS($(this), options);
	  });
	  return this;
  };

})(jQuery);

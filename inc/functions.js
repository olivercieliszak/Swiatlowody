//funkcja do kolorowania wierszy przy pomocy jQuery - jasniejszy i ciemniejszy na zmiane
function pokoloruj_wiersze(){
	$( document ).ready(function() {

		$('tr:odd').css('background-color', '#FFF');
		$('tr:even').css('background-color', '#F9F9F9');
	});
}
function pick(arg, def) {
   return (typeof arg == 'undefined' ? def : arg);
}
function rozdzielGPS(data){
	data = pick(data, '0.0,0.0');
	var ret = {};
	var nowyGPS = data.split(',');
	ret.lat = nowyGPS[0];
	ret.lon = nowyGPS[1];
	return ret;
}
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}
function getCookie(name) {
    function escape(s) { return s.replace(/([.*+?\^${}()|\[\]\/\\])/g, '\\$1'); };
    var match = document.cookie.match(RegExp('(?:^|;\\s*)' + escape(name) + '=([^;]*)'));
    return match ? match[1] : null;
}
function calcBrightness(color) {
return Math.sqrt(
   color.r * color.r * .299 +
   color.g * color.g * .587 +
   color.b * color.b * .114);          
}
function znajdzKolor(BGColor){
	BGColor = pick(BGColor, '');
	var color = new RGBColor(BGColor);
    if (color.ok) { // 'ok' is true when the parsing was a success
		var brightness = calcBrightness(color);
		var foreColor = (brightness < 160) ? "#FFFFFF" : "#000000";
	}
	else
		var foreColor = 'inherit';
		
	return foreColor;

}
function getUrlParameter(sParam)
	{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
} 


//
//
// FUNKCJE POMOCNICZE:
//
//

function print_r(o)
{
	function f(o, p, s)
	{
		for(x in o)
		{
			if ('object' == typeof o[x])
			{
				s += p + x + ' obiekt: \n';
				pre = p + '\t';
				s = f(o[x], pre, s);
			}
			else
			{
				s += p + x + ' : ' + o[x] + '\n';
			}
		}
		return s;
	}
	return f(o, '', '');
}
function Round(n, k) {
	var factor = Math.pow(10, k);
	return Math.round(n*factor)/factor;
}
function odleglosc(p1_lat, p1_lng, p2_lat, p2_lng) {
	rad = function(x) {return x*Math.PI/180;}
	var R = 6371; // earth's mean radius in km
	var dLat  = rad(p2_lat - p1_lat);
	var dLong = rad(p2_lng - p1_lng);
	var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
			Math.cos(rad(p1_lat)) * Math.cos(rad(p2_lat)) * Math.sin(dLong/2) * Math.sin(dLong/2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	var d = R * c;
	var factor = Math.pow(10, 2);
	return Math.round(d*1000*factor)/factor;
}

  
  //funkcje rozszerzajace jquery 
  //
  
	jQuery.extend
    (
       {
	   // funkcja pobiera dane w formacie JSON przez AJAXa i zwraca je do obiektu. funkcje mozna przypisac do zmiennej
          getJSONValues: function(url) 
          {
			  
              var result = null;
              $.ajax(
              {
                url: url,
                type: 'get',
                dataType: 'json',
                async: false,
                cache: false,
				timeout: 30000,
                success: function(data) 
                {
                    result = data;
                }
              });
          return result;
          }
       }
    );
	// funkcja wysyla dane metoda post i pobiera dane w formacie html przez AJAXa i zwraca je do obiektu. funkcje mozna przypisac do zmiennej
	jQuery.extend
    (
       {
          getPostJSONValues: function(url, input) 
          {
              var result = null;
              $.ajax(
              {
                url: url,
                type: 'post',
                dataType: 'json',
				data: input,
                async: false,
                cache: false,
                success: function(data) 
                {
                    result = data;
                }
              });
          return result;
          }
       }
    );

	jQuery.extend
    (
       {
          getPostValues: function(url, input) 
          {
              var result = null;
              $.ajax(
              {
                url: url,
                type: 'post',
                dataType: 'text',
				data: input,
                async: false,
                cache: false,
                success: function(data) 
                {
                    result = data;
                }
              });
          return result;
          }
       }
    );
	(function($) {
    
    // Create ExtraBox object
    function ExtraBox(el, options) {

        // Default options for the plugin:
        // attribute - the attribute that is used to match enabled and 
        //             disabled commands. Default is class. Can be any
        //             DOM attribute value
        this.defaults = {
            attribute: 'class'
        };

        this.opts = $.extend({}, this.defaults, options);
        this.$el = $(el);
        this.items = new Array();
    };

    ExtraBox.prototype = {

        //saves the list
        init: function() {
            var _this = this;
            $('option', this.$el).each(function(i, obj) {
                var $el = $(obj);
                $el.data('status', 'enabled');
                _this.items.push({
                    attribute: $el.attr(_this.opts.attribute),
                    $el: $el
                });
            });
        },
        //disabled items that match the key
        disable: function(key){
            $.each(this.items, function(i, item){
                if(item.attribute == key){
                     item.$el.remove();
                     item.$el.data('status', 'disabled'); 
                } 
            });
        },
        //enabled items that match the key
        enable: function(key){
            var _this = this;
            $.each(this.items, function(i, item){
                if(item.attribute == key){
                     
                    var t = i + 1; 
                    while(true)
                    {
                        if(t < _this.items.length) {   
                            if(_this.items[t].$el.data('status') == 'enabled')  {
                                _this.items[t].$el.before(item.$el);
                                item.$el.data('status', 'enabled');
                                break;
                            }
                            else {
                               t++;
                            }   
                        }
                        else {                                                                               _this.$el.append(item.$el);
                            item.$el.data('status', 'enabled');
                            break;
                        }                   
                    }
                } 
            });     
        }
    };

    $.fn.extraBox = function(options) {
        if (this.length) {
            this.each(function() {
                var rev = new ExtraBox(this, options);
                rev.init();
                $(this).data('extraBox', rev);
            });
        }
    };
})(jQuery);


	
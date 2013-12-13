
      
// soc_count instance array
if(typeof SOC_COUNT_INSTANCE == "undefined") {
      // The generic JSONP callback run this "associative array""
      // to complete the target.
      var SOC_COUNT_INSTANCE = {
            
            // so, because the length function doesn't existe...
            size : function() {
                  
                  var size = 0;
                
                  for (var key in this)
                        if (this.hasOwnProperty(key)) size++;
                
                  return size;
                
            }
      
      };
}


$.fn.extend({
      soc_count: function(type, target) {                  


            // * Public variable   
            // **********

            // type of query
            this.type = type;

            // select target
            this.target = target == undefined ? window.location : target;     

            // associative array id
            this.array_id = "i" + SOC_COUNT_INSTANCE.size();                                    

            // callback function string
            this.callback_str = "SOC_COUNT_INSTANCE." + this.array_id + ".callback";

            // url to call
            this.url = "";   

            // * Callback function      
            // **********
            this.callback = function (data) {


                  switch(this.type) {

                        case "twitter":
                              if(data.status == "success")
                                    $(this).html(data.story.url_count);
                              break;

                        case "facebook":
                              if(data.length > 0)
                                    $(this).html(data[0].total_count);
                              break;  
                  }
            }                          

            // * Add current instance to the array
            // **********
            SOC_COUNT_INSTANCE[ this.array_id ] = this;

            switch(type) {

                  case "twitter":
                        this.url = "http://api.tweetmeme.com/url_info.jsonc?callback=" + this.callback_str + "&url=" + this.target;
                        break;

                  case "facebook":
                        this.url = "http://api.facebook.com/restserver.php?method=links.getStats&callback=" + this.callback_str + "&format=json&urls=" + this.target;                              
                        break;      

                  default:
                        break;      

            }

            if(this.url != "") {          
                  var header = document.getElementsByTagName("head")[0];         
                  var script = document.createElement('script');
                  script.type = 'text/javascript';
                  script.src  = this.url;
                  header.appendChild(script);
            }

            return $(this);
      }
});     
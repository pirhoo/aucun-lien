
$.fn.extend({
      
      share: function (type) {
            
            var target = "";            
            var Url = {

                  // public method for url encoding
                  encode : function (string) {
                        return escape(this._utf8_encode(string));
                  },

                  // public method for url decoding
                  decode : function (string) {
                        return this._utf8_decode(unescape(string));
                  },

                  // private method for UTF-8 encoding
                  _utf8_encode : function (string) {
                        string = string.replace(/\r\n/g,"\n");
                        var utftext = "";

                        for (var n = 0; n < string.length; n++) {

                              var c = string.charCodeAt(n);

                              if (c < 128) {
                                    utftext += String.fromCharCode(c);
                              }
                              else if((c > 127) && (c < 2048)) {
                                    utftext += String.fromCharCode((c >> 6) | 192);
                                    utftext += String.fromCharCode((c & 63) | 128);
                              }
                              else {
                                    utftext += String.fromCharCode((c >> 12) | 224);
                                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                                    utftext += String.fromCharCode((c & 63) | 128);
                              }

                        }

                        return utftext;
                  },

                  // private method for UTF-8 decoding
                  _utf8_decode : function (utftext) {
                        var string = "";
                        var i = 0;
                        var c = c1 = c2 = 0;

                        while ( i < utftext.length ) {

                              c = utftext.charCodeAt(i);

                              if (c < 128) {
                                    string += String.fromCharCode(c);
                                    i++;
                              }
                              else if((c > 191) && (c < 224)) {
                                    c2 = utftext.charCodeAt(i+1);
                                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                                    i += 2;
                              }
                              else {
                                    c2 = utftext.charCodeAt(i+1);
                                    c3 = utftext.charCodeAt(i+2);
                                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                                    i += 3;
                              }

                        }

                        return string;
                  }
            
            }

            
            switch (type) {
                  
                  case "twitter":
                        
                        $(this).click(function () {
                              
                              var $this = $(this);

                              // service is Twitter
                              if( $this.data("tweet-id") ) {
                                    var target = "http://twitter.com/intent/retweet?"; 
                                    target += "tweet_id=" + $this.attr("data-tweet-id");                                    
                              } else { 

                                    var target = "http://twitter.com/share?";     
                                    // URL of the page to share
                                    var url  = $this.attr("data-url");   

                                    if(url != undefined)
                                          target += "&url="+Url.encode(url);
                                    else
                                          target += "&url="+escape(window.location);
                                    
                                    
                                    // Screen name of the user to attribute the Tweet to
                                    var via  = $this.attr("data-via");
                                    if(via != undefined)
                                          target += "&via="+Url.encode(via);
                                    
                                    // Default Tweet text
                                    var text = $this.attr("data-text");  
                                    if(text != undefined)
                                          target += "&text="+Url.encode(text); 
                                   
                                   
                                    // The URL to which your shared URL resolves to
                                    var counturl = $this.attr("data-counturl");
                                    if(counturl != undefined)
                                          target += "&counturl="+Url.encode(counturl); 
                              }

                              // Related accounts
                              var related = $this.attr("data-related");
                              if(related != undefined)
                                    target += "&related="+Url.encode(related); 
                               
                              window.open(target, "sharer", "toolbar=no, width=600, height=400, location=no");
                              
                              return false;

                        });
                        break;
                        
                  case "facebook":
                        
                        $(this).click(function () {
                              
                              // service is Facebook
                              target = "http://www.facebook.com/sharer.php?";    
                                                           
                              // URL of the page to share
                              var url  = $(this).attr("data-url");   
                              if(url != undefined)
                                    target += "&u="+Url.encode(url);
                              else
                                    target += "&u="+escape(window.location);
                              
                              // Default Tweet text
                              var text = $(this).attr("data-text");  
                              if(text != undefined)
                                    target += "&t="+Url.encode(text); 
                             
                              window.open(target, "sharer", "toolbar=no, width=600, height=400, location=no");
                              
                              return false;

                        });
                        break;

                  case "google":
                        
                        $(this).click(function () {
                              
                              // service is Facebook
                              target = "https://plus.google.com/share?";    
                                                           
                              // URL of the page to share
                              var url  = $(this).attr("data-url");   
                              if(url != undefined)
                                    target += "&url="+Url.encode(url);
                              else
                                    target += "&url="+escape(window.location);                              
                             
                              window.open(target, "sharer", "toolbar=no, width=600, height=400, location=no");
                              
                              return false;

                        });
                        break;

                  case "pinterest":

                        $(this).click(function() {
                              
                              var $this = $(this);

                              // service is Facebook
                              target = "http://pinterest.com/pin/create/button?";    
                                                           
                              // URL of the page to share
                              var url  = $(this).attr("data-url");   
                              if(url != undefined)
                                    target += "&url="+Url.encode(url);
                              else
                                    target += "&url="+escape(window.location);
                              
                              // Default Tweet text
                              var text = $(this).attr("data-text");  
                              if(text != undefined)
                                    target += "&description="+Url.encode(text); 

                              // The URL of the media to share
                              var media = $this.attr("data-media");
                              if(media != undefined)
                                    target += "&media="+Url.encode(media); 
                             
                              window.open(target, "sharer", "toolbar=no, width=600, height=400, location=no");
                              
                              return false;

                        });
                        break;
                        
                        
                  default:
                        break;
                  
            }
            
            
            return this;
            
            
      }
});
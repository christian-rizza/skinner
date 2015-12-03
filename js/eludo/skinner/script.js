var objects = new Array();
var canvas;
var stage;
var after_login;    //Function to log after login operation

var hash = new Object();
var container;


function JQueryAlias($) {
    $(document).ready(function()
    {
        canvas = document.getElementById("canvas");
        stage = new createjs.Stage("canvas");
        
        //testImage();
        onProductConfirm();
        //openRetrieve();
        //openImage();
        loadImages();
        
        //Setting UI interfaces
        $(".skinner_spinner").spinner();
        $(".skinner_dialog" ).dialog({autoOpen:false});
        $(".skinner_container input[type=submit], .skinner_container a, .skinner_container button" ).button().click(function( event ) {event.preventDefault();});
        
        //Setting UI interface handlers
        $( "#btn-image" ).click(openImage);
        $("#images").bind("change", onImageChange);
        $("#dialog-image .btn-confirm").bind("click", onImageConfirm);
        $('#loadimg-confirm').attr("disabled","disabled");
        $('#loadimg-confirm').bind("click", onImageLoadConfirm);
        $('#loadimg-file').bind("change", onImageLoadChange);

        $("#btn-text" ).click(openText);
        $("#texts").bind("change", onTextChange);
        $("#dialog-text .btn-confirm").bind("click", onTextConfirm);

        $("#btn-product" ).click(openProduct);
        $("#products").bind("change", onProductChange)
        $("#dialog-product .btn-confirm").bind("click", onProductConfirm);
        
        $("#btn-save" ).click(openSave);
        $("#btn-retrieve").click(openRetrieve);
        $("#btn-buy").click(openBuy);
        $("#btn-download" ).click(openDownload);
        
        $("#dialog-login .btn-confirm").bind("click", onLoginConfirm);
        
        /**
         * Load images from server and draw on images dialog box
         * @returns {undefined}
         */
        function loadImages()
        {
            $.ajax({
                url: baseurl + 'skinner/index/getImage',
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: false,
                error: function(jqXHR, textStatus, errorThrown) {
                    
                    alert(errorThrown + "\r\n" + jqXHR.statusText + "\r\n" + jqXHR.responseText);
                    
                },
                beforeSend: function(xhr) {
                    
                    console.log("sto inviando");
                },
                success: function(data, textStatus, jqXHR) {
                    
                    if (data)
                    {
                        var select_html = '';
                        for(var i=0;i<data.length;i++)
                        {
                            var selected = "";
                            if (i===0) selected = 'selected="selected"';
                            select_html+= '<option value="'+data[i].image_url+'" '+selected+'>'+data[i].title+'</option>';
                        }
                        $('#images').html(select_html);
                        
                        onImageChange();
                    }
                }
                
                
            });
        }
        
        /**
         * Send canvas data to server in order to serialize image and download it
         * @returns {undefined}
         */
        function openDownload()
        {
            var data = canvas.toDataURL("image/png");
            data = data.substr(data.indexOf(',') + 1).toString();
         
            var dataInput = document.createElement("input");
            dataInput.setAttribute("name", 'imgdata');
            dataInput.setAttribute("value", data);
            dataInput.setAttribute("type", "hidden");
         
            var nameInput = document.createElement("input");
            nameInput.setAttribute("name", 'name');
            nameInput.setAttribute("value", 'skinner_product.png');
         
            var myForm = document.createElement("form");
            myForm.method = 'post';
            myForm.action = baseurl + 'skinner/index/downloadImage';
            myForm.appendChild(dataInput);
            myForm.appendChild(nameInput);
         
            document.body.appendChild(myForm);
            myForm.submit();
            document.body.removeChild(myForm);
        }
        /**
         * Save product image.
         * This image will be added to new product in cart 
         * @param {type} callback
         * @returns {undefined}
         */
        function openSaveImage(callback)
        {
            var product_id = $('#skinner_productid').val();
            var data = canvas.toDataURL("image/png");
            data = data.substr(data.indexOf(',') + 1).toString();
            
            var formData = new FormData();
            formData.append("name", product_id);
            formData.append("imgdata",data);
            
            $.ajax({

                url: baseurl + 'skinner/index/saveImage',
                type: 'POST',
                data: formData,
                processData: false, // Don't process the files
                contentType: false,
                success: function(data, textStatus, jqXHR) {

                    if (callback) callback();

               },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    alert(errorThrown + "\r\n" + jqXHR.statusText + "\r\n" + jqXHR.responseText);
                }
            });
        }
        /**
         * Open Product dialog box
         * @returns {undefined}
         */
        function openProduct()
        {
            openWindow($("#dialog-product"), $('.skinner_content'));
        }
        /**
         * Open text dialog box
         * @returns {undefined}
         */
        function openText()
        {
            $("#dialog-properties").dialog("close");
            openWindow($("#dialog-text"), $('.skinner_content'));
            loadFonts("font-name","font-name");
            
        }
        /**
         * Open image dialog box
         * @returns {undefined}
         */
        function openImage()
        {
            $("#dialog-properties").dialog("close");
            openWindow($("#dialog-image"), $('.skinner_content'));
            onImageChange();
        }
        /**
         * Open Login dialog box
         * @param {type} callback
         * @returns {undefined}
         */
        function openLogin(callback)
        {
            after_login = callback;
            
            $("#dialog-properties").dialog("close");
            openWindow($("#dialog-login"), $('.skinner_content'), true);
        }
        /**
         * Open save dialog box
         * if user is not logged in will be opened a login box
         * @returns {undefined}
         */
        function openSave()
        {
            openWindow($("#dialog-wait"), $('.skinner_content'));
            $.getJSON(baseurl+'skinner/index/checkLogged', function(data)
            {
                $("#dialog-wait").dialog("close");
                if (!data.result)
                {
                    openLogin(openSave);
                }
                else
                {
                    var formData = new FormData();
                    formData.append("product_id", $('#skinner_productid').val());
                    formData.append("data",JSON.stringify(objects));

                    $.ajax({
                        
                        url: baseurl + 'skinner/index/saveData',
                        type: 'POST',
                        data: formData ,
                        dataType: 'json',
                        processData: false, // Don't process the files
                        contentType: false,
                        success: function(data, textStatus, jqXHR) {
                            
                            alert("Product Saved");
                            openSaveImage();

                       },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            alert(errorThrown + "\r\n" + jqXHR.statusText + "\r\n" + jqXHR.responseText);
                        }
                    });
                }
            });
        }
        
        /**
         * Load data to show in canvas
         * @param {type} data
         * @returns {undefined}
         */
        function loadData(data)
        {
            if (data.success)
            {
                for (var i=0;i<data.success.length;i++)
                {
                    var obj = data.success[i];
                    
                    if (obj.name==="SkinnerImage")
                    {
                        var image = new SkinnerImage(stage, obj.url);
                        image.order = i;
                        image.x = obj.x;
                        image.y = obj.y;
                        image.scaleX = obj.scaleX;
                        image.scaleY = obj.scaleY;
                        image.rotation = obj.rotation;

                        image.addEventListener('click', function(event)
                        {
                            if (!image.isMove)
                            {
                                openProperties(event.target);
                            }
                        });

                        image.draw();
                        objects.push(image);
                    }
                    else if (obj.name==="SkinnerText")
                    {
                        var font_name = obj.font_name;
                        var font_size = obj.font_size;
                        var font_color = obj.font_color;
                        var text_string = obj.text_string;

                        var text = new SkinnerText(stage, text_string, font_name, font_size, font_color);
                        text.order=i;
                        text.x = obj.x;
                        text.y = obj.y;
                        text.rotation = obj.rotation;

                        text.addEventListener('click', function(event)
                        {
                            openProperties(event.target);
                        });
                        
                        text.draw();

                        objects.push(text);
                    }
                }
            }
            else
            {
                onProductConfirm();
            }
            
            setTimeout(function()
            {
                for (var i=0;i<objects.length;i++)
                {
                    objects[i].setDepth(i);
                    objects[i].draw();
                }
                stage.update();
                $("#dialog-wait").dialog("close");
            }, 1000);
            
        }
        
        /**
         * retrieve saved data
         * @returns {undefined}
         */
        function openRetrieve()
        {
            openWindow($("#dialog-wait"), $('.skinner_content'));
            $.getJSON(baseurl+'skinner/index/checkLogged', function(data)
            {
                if (!data.result)
                {
                    $("#dialog-wait").dialog("close");
                    openLogin(openRetrieve);
                }
                else
                {
                    cleanStage();

                    var formData = new FormData();
                    formData.append("product_id", $('#skinner_productid').val());

                    $.ajax({

                        url: baseurl + 'skinner/index/loadData',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        processData: false, // Don't process the files
                        contentType: false,
                        success: function(data, textStatus, jqXHR) {

                            loadData(data);

                       },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            alert(errorThrown + "\r\n" + jqXHR.statusText + "\r\n" + jqXHR.responseText);
                            $("#dialog-wait").dialog("close");
                        }
                    });
                }
            });
        }
        /**
         * send image canvas and add to cart in order to purchase products
         * @returns {undefined}
         */
        function openBuy()
        {
            openWindow($("#dialog-wait"), $('.skinner_content'));
            $.getJSON(baseurl+'skinner/index/checkLogged', function(data)
            {
                if (!data.result)
                {
                    openLogin(openBuy);
                }
                else
                {
                   
                    var product_id = $('#skinner_productid').val();
                    var data = canvas.toDataURL("image/png");
                    data = data.substr(data.indexOf(',') + 1).toString();

                    var formData = new FormData();
                    formData.append("product_id", product_id);
                    formData.append("imgdata",data);

                    $.ajax({
                        url: baseurl + 'skinner/index/addToCart',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        processData: false, // Don't process the files
                        contentType: false,
                        success: function(data, textStatus, jqXHR) {

                            if (data['success'])
                            {
                                window.location.replace(data["success"]);
                            }
                            else
                            {
                                $("#dialog-wait").dialog("close");
                                $('.skinner_footer').before('<div>' + data['error'] + '</div>');
                            }

                        },
                        error: function(jqXHR, textStatus, errorThrown)
                        {
                            alert(errorThrown + "\r\n" + jqXHR.statusText + "\r\n" + jqXHR.responseText);
                        }
                    });
                }
            });
        }
        
        /****************************
         * Event Handler
         ***************************/
        
        /**
         * Function called when user click on image load button
         * The fuction call loadimges to refresh images dialog box
         * @returns {undefined}
         */
        function onImageLoadConfirm()
        {
            $('#loadimg-confirm').attr("disabled", "disabled");
            $('#dialog-image .btn-confirm').attr("disabled", "disabled");
            
            $.getJSON(baseurl+'skinner/index/checkLogged', function(data)
            {
                if (!data.result)
                {
                    $("#dialog-wait").dialog("close");
                    $("#dialog-image").dialog("close");
                    $('#loadimg-confirm').removeAttr("disabled");
                    $('#dialog-image .btn-confirm').removeAttr("disabled");
                    openLogin(openImage);
                }
                else
                {
                    var formData = new FormData();
                    formData.append('loadimage', $('#loadimg-file')[0].files[0]);

                    $.ajax({
                        url: baseurl + 'skinner/index/loadImage',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        processData: false, // Don't process the files
                        contentType: false,
                        error: function(jqXHR, textStatus, errorThrown) {

                            $('#loadimg-confirm').removeClass("disabled");

                            alert(errorThrown + "\r\n" + jqXHR.statusText + "\r\n" + jqXHR.responseText);

                        },
                        beforeSend: function(xhr) {

                            console.log("sto inviando");
                        },
                        success: function(data, textStatus, jqXHR) {

                            $('#loadimg-confirm').removeAttr("disabled");
                            $('#dialog-image .btn-confirm').removeAttr("disabled");

                            $('#loadimg-file').val("");
                            loadImages();

                        }
                    });
                }
            });
            
            
            
            
            
        }
        
        /**
         * Diable and enable loading button when input file changing
         * @returns {undefined}
         */
        function onImageLoadChange()
        {
            if ($('#loadimg-file').val())
            {
                $('#loadimg-confirm').removeAttr("disabled");
            }
            else
            {
                $('#loadimg-confirm').removeAttr("disabled");
                $('#loadimg-confirm').attr("disabled","disabled");
            }
        }
        
        /**
         * On press login button
         * @returns {undefined}
         */
        function onLoginConfirm()
        {
            var formData = new FormData();
            formData.append($('#skinner_formkey').attr("name"),$('#skinner_formkey').val());
            formData.append($('#skinner_username').attr("name"),$('#skinner_username').val());
            formData.append($('#skinner_password').attr("name"),$('#skinner_password').val());
            
            $.ajax({
                
                url: baseurl + 'customer/account/loginPost/',
                type: 'POST',
                data: formData,
                processData: false, // Don't process the files
                contentType: false,
                success: function(data, textStatus, jqXHR) {

                    $("#dialog-login").dialog("close");
                    
                    if (after_login)
                    {
                        after_login();
                        after_login=null;
                    }

                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    alert(errorThrown + "\r\n" + jqXHR.statusText + "\r\n" + jqXHR.responseText);
                }
            });
        }
        
        /**
         * Clean stage and canvas elements
         * @returns {undefined}
         */
        function cleanStage()
        {
            stage.removeAllChildren();
            objects = new Array();
            stage.update();

        }
        /**
         * on product selection
         * @returns {undefined}
         */
        function onProductConfirm()
        {
            cleanStage();
            
            var url = jQuery('#dialog-product img').attr("src");
            var image = new SkinnerImage(stage, url);
            image.x = canvas.width/2;
            image.y = canvas.height/2;
            image.scaleX = image.scaleY = 0.8;

            //$("#dialog-image").dialog("close");
            image.draw();
            
            try
            {
                $("#dialog-product").dialog("close");
            }
            catch (e) {}
            
            objects.push(image);
        }
        /**
         * 
         * @returns {undefined}
         */
        function onImageConfirm()
        {
            
            var mask_width = $('#mask_width').val();
            var mask_height = $('#mask_height').val();
            
            var url = jQuery('#dialog-image img').attr("src");
            var image = new SkinnerImage(stage, url, mask_width, mask_width);
            image.order = objects.length,
            image.x = canvas.width/2;
            image.y = canvas.height/2;
            image.scaleX = $("#spinner_img-scale").val();
            image.scaleY = $("#spinner_img-scale").val();

            image.addEventListener('click', function()
            {
                if (!image.isMove)
                {
                    openProperties(image);
                }
                image.isMove=false;
                
            });
            
            try
            {
                $("#dialog-image").dialog("close");
            }
            catch(e){}
            image.draw();
            
            objects.push(image);
            
            
        }
        
        /**
         * 
         * @returns {undefined}
         */
        function onTextConfirm()
        {
            
            var mask_width = $('#mask_width').val();
            var mask_height = $('#mask_height').val();
            
            
            var val = jQuery('#skinner_text').val();
            if (val.trim() === "")
                val = jQuery('#texts').val();
            
            var font_name = jQuery('#font-name').val();
            var font_size = jQuery('#font-size').val();
            var font_color = jQuery('#font-color').val();
            
            var image = new SkinnerText(stage, val, font_name, font_size, font_color, mask_width, mask_height);
            image.order = objects.length,
            image.x = canvas.width/2;
            image.y = canvas.height/2;

            image.addEventListener('click', function()
            {
                if (!image.isMove)
                {
                    openProperties(image);
                }
                image.isMove=false;
            });
            
            try
            {
                $("#dialog-text").dialog("close");
            }
            catch(e){}
            
            image.draw();
            
            objects.push(image);
            
        }
        /**
         * 
         * @returns {undefined}
         */
        function onProductChange()
        {
            jQuery('#dialog-product img').attr("src", this.value);
        }
        /**
         * 
         * @returns {undefined}
         */
        function onTextChange()
        {
            jQuery('#skinner_text').val(this.value);
        }
        /**
         * 
         * @returns {undefined}
         */
        function onImageChange()
        {
            jQuery('#dialog-image img').attr("src", jQuery('#images').val());
            
            var image = new Image();
            image.onload = function()
            {
                jQuery('#spinner_img-scale').val(1);
                jQuery('#spinner_img-width').val(this.width);
                jQuery('#spinner_img-height').val(this.height);
            };
            image.src = jQuery('#dialog-image img').attr("src");
           

        }
        /**
         * 
         * @param {type} object
         * @returns {undefined}
         */
        function openProperties(object)
        {
            if (object.order==0) return;
            
            //if (object.!=isMove) return;
            
            openWindow($("#dialog-properties"), $('.skinner_content'));
            
            $('#dialog-properties .content').empty();
            $('#dialog-properties .skinner_button').empty();
            
            var proprieta = object.getProperties();
            for (var i=0;i<proprieta.length;i++)
            {
                var skinner_class="";
                //Creo la per il tipo di dato
                if (proprieta[i]==='font_color')
                {
                    skinner_class = "skinner_color";
                }
                else if (proprieta[i]==='font_string')
                {
                    skinner_class = "skinner_font";
                }
                
                
                $('#dialog-properties .content').append('<label>'+proprieta[i]+'</label>');
                $('#dialog-properties .content').append('<input type="text" value="'+object[proprieta[i]]+'" class="properties '+skinner_class +'" id="'+i+'" />');
                
                if (skinner_class=="skinner_font") loadFonts("skinner_font", i);
                if (skinner_class=="skinner_color") loadColorPicker();
                
                $('#dialog-properties #'+i).change(function()
                {
                    
                    var valore = $(this).val();
                    
                    if (!isNaN(valore)) valore = parseNumber(valore);
                    
                    object[proprieta[this.id]] = valore;
                    object.draw();
                });                
            }
            
            $('#dialog-properties .skinner_button').append('<input type="button" value="Delete" class="button btn-delete" />');
            $('#dialog-properties .btn-delete').bind("click",function()
            {
                var index = objects.indexOf(object);
                object.delete();
                objects.splice(index, 1);
                $("#dialog-properties").dialog("close");
            });
        }
        /**
         * 
         * @param {type} class_name
         * @param {type} id
         * @returns {undefined}
         */
        function loadFonts(class_name, id)
        {
            
            var fonts_url = "Source+Sans+Pro|Droid+Sans|Lato|PT+Sans|Droid+Serif|Open+Sans|Roboto|Oswald|Open+Sans+Condensed:300|Roboto+Condensed";
            fonts_url = fonts_url.split("+").join(" ");
            var fonts = fonts_url.split("|");

            var select_html="";
            for(var j=0;j<fonts.length;j++)
            {
                select_html+= '<option value="'+fonts[j]+'" >'+fonts[j]+'</option>';
            }
            $('.'+class_name).replaceWith('<select name="font-name" class="'+class_name+'" id="'+id+'"></select>');
            $('.'+class_name).html(select_html);
        }
        /**
         * 
         * @returns {undefined}
         */
        function loadColorPicker()
        {
            jscolor.bind();
        }
        /**
         * Show dialog box
         * @param {type} element to make dialog
         * @param {type} elem_container 
         * @param {type} modal mode true or not (default true)
         * @returns {undefined}
         */
        function openWindow(element, elem_container, modal)
        {   
            element.dialog({open: onWindowOpened});
            element.dialog({close:onWindowClosed});
            element.dialog("option", "resizable", false);
            element.dialog("option", "height", "auto");
            element.dialog("option", "modal", true)
            
            element.dialog("open");
        }
        /**
         * 
         * @param {type} event
         * @param {type} ui
         * @returns {undefined}
         */
        function onWindowClosed(event, ui)
        {
            
        }
        /**
         * 
         * @param {type} event
         * @param {type} ui
         * @returns {undefined}
         */
        function onWindowOpened(event, ui)
        {
            
        }
    });
};

JQueryAlias(jQuery);

function isHexaColor(sNum){
  return (typeof sNum === "string") && sNum.length === 6 
         && ! isNaN( parseInt(sNum, 16) );
}


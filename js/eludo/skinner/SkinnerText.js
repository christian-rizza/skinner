SkinnerText.prototype = new Drawable();
createjs.EventDispatcher.initialize(SkinnerText.prototype);

function SkinnerText(stage, text_string, font_string, font_size, font_color, w, h)
{
    if (w) this.w = w;
    else this.w = 150;
    
    if (h) this.h = h;
    else this.h = 200;
    
    this.text;
    this.stage = stage;

    if (!font_size) font_color = "12";
    if (!font_string) font_color = "Arial";
    if (!font_color) font_color = "#000000";
 
    this.name="SkinnerText";
    this.font_string = font_string;
    this.font_size = font_size;
    this.font_color = font_color;
    this.text_string = text_string;
    
    this.inzialized = false;
    
    this.delete = function()
    {
        if (this.text)
        {
            if (this.star) this.stage.removeChild(this.star);
            
            this.stage.removeChild(this.text);
            this.stage.update();
            this.text = null;
        }
    };
    this.enableDrag = function () {

        // OnPress event handler
        var self = this;
        if (!this.text.hasEventListener("mousedown"))
        {
            this.text.addEventListener("mousedown", function(evt) {

                var offset = {x: self.text.x - evt.stageX, y: self.text.y - evt.stageY};

                // Bring to front
                self.stage.addChild(self.text);

                if (!self.text.hasEventListener("pressmove")) 
                {
                    var self2 = self;
                    self.text.addEventListener("pressmove", function(ev)
                    {
                        self2.isMove = true;
                        self2.x = ev.stageX + offset.x;
                        self2.y = ev.stageY + offset.y;//-self2.image.height/2);
                        self2.draw();
                    });
                }
            });
        }
    }
    this.draw = function()
    {
        if (this.text)
        {
            
            this.text.text = this.text_string;
            this.text.font = "bold "+this.font_size+"px "+this.font_string;
            this.text.color = this.font_color;
            
            this.text.regX = this.text.getMeasuredWidth() / 2;
            this.text.regY = this.text.getMeasuredHeight() / 2;
            this.text.scaleX = this.scaleX;
            this.text.scaleY = this.scaleY;
            this.text.rotation = this.rotation;

            this.text.x = this.x;// - this.image.width*this.scaleX/2;
            this.text.y = this.y;// - this.image.height*this.scaleY/2;
            
            if (this.star) this.stage.removeChild(this.star);
            
            // masks can only be shapes.
            var star = new createjs.Shape();
            // the mask's position will be relative to the parent of its target:
            star.x = this.stage.children[0].x;
            star.y = this.stage.children[0].y;
            // only the drawPolyStar call is needed for the mask to work:
            star.graphics.beginStroke("#000").setStrokeStyle(0.1).drawRect(-this.w/2, -this.h/2, this.w, this.h);
            
            
            this.stage.addChild(star);
            
            this.text.mask = star;
            this.star = star;

            this.stage.update();
        }
        else
        {
            var self = this;
            this.font = "bold "+this.font_size+"px "+this.font_string;
            this.text = new createjs.Text(this.text_string, this.font, this.font_color);
            this.stage.addChildAt(this.text, this.order);
            this.text.addEventListener("click", function()
            {
                self.dispatchEvent("click");
            });
            this.draw();
            this.enableDrag();
        }
    };
    this.getProperties = function()
    {
        var params = new Array("text_string","font_string","font_color","font_size","x","y","rotation");
        return params;
    };
    
    this.toJSON = function()
    {
        return {name: this.name, text_string: this.text_string, font_string: this.font_string, font_color: this.font_color, font_size: this.font_size, x: this.x, y:this.y, scaleX: this.scaleX, scaleY:this.scaleY, rotation: this.rotation};
    };
    
    this.setDepth = function(depth)
    {
        this.order = depth;
        this.stage.addChildAt(this.text, this.order);
    };
}
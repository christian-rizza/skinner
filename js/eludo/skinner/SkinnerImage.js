SkinnerImage.prototype = new Drawable();
createjs.EventDispatcher.initialize(SkinnerImage.prototype);

function SkinnerImage(stage, url, w, h)
{
    if (w) this.w = w;
    else this.w = 150;
    
    if (h) this.h = h;
    else this.h = 200;
    
    this.stage = stage;
    this.name="SkinnerImage";
    this.url = url;
    this.inzialized=false;
    this.image;
    
    
    this.delete = function()
    {
        if (this.star) this.stage.removeChild(this.star);
        this.stage.removeChild(this.image);
        this.stage.update();
    };
    this.enableDrag = function () {

        // OnPress event handler
        var self = this;
        if (!this.image.hasEventListener("mousedown"))
        {
            this.image.addEventListener("mousedown", function(evt) {

                var offset = {x: self.image.x - evt.stageX, y: self.image.y - evt.stageY};

                // Bring to front
                self.stage.addChild(self.image);

                if (!self.image.hasEventListener("pressmove")) 
                {
                    var self2 = self;
                    self.image.addEventListener("pressmove", function(ev)
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
        if (this.image)
        {
            this.image.regX = this.image.width/2;
            this.image.regY = this.image.height/2;
            this.image.scaleX = this.scaleX;
            this.image.scaleY = this.scaleY;
//            this.image.rotation = this.rotation;
//            
            this.image.x = this.x;
            this.image.y = this.y;
            
            if (this.order!==0)
            {
                if (!this.image.mask)
                {
                    if (this.star) this.stage.removeChild(this.star);
                
                    var star = new createjs.Shape();
                    star.x = this.stage.children[0].x;
                    star.y = this.stage.children[0].y;
                    star.graphics.beginStroke("#000").setStrokeStyle(0.1).drawRect(-this.w/2, -this.h/2, this.w, this.h);
                    this.stage.addChild(star);
                    this.image.mask = star;
                    
                    this.star = star;
                }

//                var image1 = new Image();
//                var self = this;
//                image1.onload = function(event)
//                {
//                    var image = new createjs.Bitmap(image1);
//                    
//                    self.image.regX = this.width/2;
//                    self.image.regY = this.height/2;
//                    self.image.scaleX = 0.8;
//                    self.image.scaleY = 0.8;
//                    
//                    stage.update();
//                    
//                    var amf = new createjs.AlphaMaskFilter(image.image);
//                    
//                    self.image.filters = [amf];
//                    self.image.cache(0, 0, image.image.width, image.image.height);
//                    
//                    
//                    self.stage.update();
//
//                };
//                image1.src = "http://localhost/skinner_front.png";

            }
            
            
            this.stage.update();
        }
        else
        {
            var self = this;
            var imageObj = new Image();
            imageObj.onload = function()
            {
                
                self.image = new createjs.Bitmap(this);
                self.image.width = this.width;
                self.image.height = this.height;
                self.stage.addChildAt(self.image, self.order);
                
                self.image.addEventListener("click", function (event) 
                {
                    self.dispatchEvent("click");
                });
                self.draw();
                if (self.order!==0) self.enableDrag();
            };
            imageObj.src = this.url;
            this.s_image = imageObj;
        }
        
    };
    
    this.toJSON = function()
    {
        return {name: this.name, url: url, x: this.x, y:this.y, scaleX: this.scaleX, scaleY:this.scaleY, rotation: this.rotation};
    };
    
    this.setDepth = function()
    {
        this.stage.addChildAt(this.image, this.order);
    };
}
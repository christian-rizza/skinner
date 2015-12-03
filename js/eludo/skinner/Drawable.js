function Drawable()
{   
    this.x = 0;
    this.y = 0;
    this.scaleX = 1;
    this.scaleY = 1;
    this.rotation = 0;
    this.order = 0;
    this.isMove = false;
    
    this.setPosition = function(x, y)
    {
        this.x = x;
        this.y = y;
    };
    this.setScale = function(scaleX, scaleY)
    {
        this.scaleX = scaleX;
        this.scaleY = scaleY;
    };
    this.setRotation = function(rotation)
    {
        this.rotation = rotation;
    };
    this.getProperties = function()
    {
        var params = new Array("x","y","scaleX","scaleY","rotation");
        return params;
    };
    this.canMove = function()
    {
        return !isMove;
    };
}
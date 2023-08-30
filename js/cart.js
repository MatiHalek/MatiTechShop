var cart = {
    products : new Array(),
    read : function(){
        if(localStorage.getItem("cart"))
        {
            this.products = JSON.parse(localStorage.getItem("cart"));
            return this.products;
        }
        return null;
    },    
    countProducts : function(){
        if(localStorage.getItem("cart"))
        {           
            var counter = 0;
            this.products.forEach(function(el){
                counter += parseInt(el.amount);
            });
            if(document.querySelector("#cartItemCounter") && counter > 0)
            {
                document.querySelector("#cartItemCounter").textContent = counter;
                document.querySelector("#cartItemCounter").style.display = "block";
            }           
            return counter;
        }
        return null;
    },      
    addProduct : function(id, amount){
        if(localStorage.getItem("cart"))
        {
            this.products = JSON.parse(localStorage.getItem("cart"));
            var exists = undefined;
            for(var i = 0; i < this.products.length; i++)
            {
                if(this.products[i].productId == id)
                {
                    exists = i;
                    break;
                }
            }
            if(exists === undefined)
                this.products.push({"productId" : id, "amount" : amount});
            else
                this.products[i].amount += amount;
            localStorage.setItem("cart", JSON.stringify(this.products));
            this.countProducts();
        }
        else
            return null;
    },
    updateProduct : function(id, quantity){
        if(localStorage.getItem("cart"))
        {
            this.products = JSON.parse(localStorage.getItem("cart"));
            for(var i = 0; i < this.products.length; i++)
            {
                if(this.products[i].productId == id)
                {
                    this.products[i].amount = parseInt(quantity);
                    break;
                }
            }
            localStorage.setItem("cart", JSON.stringify(this.products));
            this.countProducts();
        }
        else
            return null;           
    },
    removeProduct : function(id){
        if(localStorage.getItem("cart"))
        {
            this.products = JSON.parse(localStorage.getItem("cart"));
            var index = false;
            for(var i = 0; i < this.products.length; i++)
            {
                if(this.products[i].productId == id)
                {
                    index = i;
                    break;
                }
            } 
            this.products.splice(i, 1);
            localStorage.setItem("cart", JSON.stringify(this.products));
            this.countProducts();
        }
        else
            return null;
    }, 
    removeAll : function(){
        localStorage.removeItem("cart");
        this.products = new Array();
        this.countProducts();
    }
};  
if(localStorage.getItem("cart"))
    cart.read(); 
else
    localStorage.setItem("cart", JSON.stringify(new Array()));
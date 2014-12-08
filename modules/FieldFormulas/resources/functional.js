/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ******************************************************************************/
function functional(){}

functional.prototype = {
    
    /**
     * Test:
     * fn.format("Hello %s", "world") == "Hello world"
     */
    format: function(){
        var i=1;
        var fmtStr = arguments[0];
        var args = arguments;
        return fmtStr.replace(/%s/g,function(){return args[i++];})
    },
    
    



	addStylesheet: function(url){
		/*From: http://www.hunlock.com/blogs/Howto_Dynamically_Insert_Javascript_And_CSS*/
		var headID = document.getElementsByTagName("head")[0];         
		var cssNode = document.createElement('link');
		cssNode.type = 'text/css';
		cssNode.rel = 'stylesheet';
		cssNode.href = url;
		cssNode.media = 'screen';
		headID.appendChild(cssNode);
	},

    /*
     *Convert the last parameter into a list argument
     */

    
    /**
     * Internal function for handling function arguments
     *
     * Test:
     * fn.args("a","b","*c")(function(args){return args;})(1,2,3,4)=={"a":1,"b":2,"c":[3,4]};
     */
     /*
    args: function(){
        if(arguments[arguments.length-1][0]=="*"){
            args = arguments[0,-1];
            larg = arguments[arguments.length-1].slice(1);
        }else{
            args = arguments;
            larg = null;
        }
        return function(callable){
            return {
                arr=new Object();
                for(var i =1;i<args.length;i++){
                    arr[arguments[i]] = args[i-1];
                }
            }
        }
        return arr;
    }*/
    
    larg: function (fn){
    	var arity = fn.arity;
    	var nparams = arity-1;
        return function(){
    		if(nparams>arguments.length){
    			nparams = arguments.length;
    		}

    		var args = [];	
    		for(var i=0;i<nparams;i++){
    			args[i] = arguments[i];
    		}

    		var largs = [];
    		alert(arguments.length-nparams);
    		for(var i=0, n=arguments.length-nparams;i<n;i++){
    			largs[i]=arguments[nparams+i];
    		}
    		args[args.length]=largs;
    		return fn.apply(this, args);
    	}
    }
    

}

fn = new functional();
/*
larg = fn.larg;
itertools.prototype = {
    
function _itertools(fn){
    iter = function(val){
        function ArrayIterator(arr){
            var idx = 0;
            return {
                next(){
                    if(arr.length==idx){
                        throw new StopIteration();
                    }else{
                        return arr[i++];
                    }
                }
            }
        }
        if(val instanceof Array){
            return ArrayIterator(val);
        }else{
            throw new Exception();
        }
    };
        
    map = function(fn, iter){
        return {
            next: function(){
                return(fn(iter.next()));
            }
        };
    };
    
    zip = larg(function(arrs){
        return list(map(function(iter){return iter.next()}, arrs));
    });
    
    return {
        iter: iter,
        zip: zip,
        map: map
    };
}

itertools = _itertools(fn);
*/
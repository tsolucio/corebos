var express = require('express'),
    http = require('http'),
    https = require('https'),
    path = require('path'),
    ioServer = require('socket.io'),
    NodeConfig = require('./routes/nodeconfig'),
    request = require("request"),
    fs = require('fs'),
    app = express(),
    replace = require("replace"),
    passport = require('passport'),
    cors = require("cors");
    LocalStrategy = require('passport-local').Strategy,
    bodyParser = require('body-parser'),
    //sslConfig = require('./ssl-config'),
    port = process.env.PORT || 7000;

//var credentials = {key: sslConfig.privateKey, cert: sslConfig.certificate};

//
//allowCrossDomain = function(req, res, next) {
//    res.header('Access-Control-Allow-Origin', 'localhost');
//    res.header('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS');
//    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization, Content-Length, X-Requested-With');
//    if ('OPTIONS' === req.method) {
//        res.send(200);
//    } else {
//        next();
//    }
//}


app.configure(function () {
    var config = {
    "cors_allowed_domains": ["http://localhost","www.huknow.com"],
    };
    var corsOptions = {
    "origin": config.cors_allowed_domains,
    "methods": "GET,HEAD,PUT,PATCH,POST,DELETE",
    "preflightContinue": false,
    "credentials": true
    };
    app.use(cors(corsOptions));
    app.use(express.bodyParser({limit: '500mb'}));
    app.use(express.methodOverride());
    app.use(express.cookieParser());
    app.use(
        require('express-session')(
            {
                name: 'site_cookie',
                secret: 'secret',
                resave: false,
                saveUninitialized: false,
                cookie: {
                    maxAge: 14400000
                }
            }

        )
    );
    app.use(passport.initialize()); // Add passport initialization
    app.use(passport.session());    // Add passport initialization
    app.use(bodyParser.json({limit: '500mb'})); // support json encoded bodies
    app.use(bodyParser.urlencoded({limit: '500mb', extended: true }));

});


if (NodeConfig.type=='ssl'){
    var server = https.createServer(credentials, app);
} else {
    server = http.createServer(app);
}
server.listen(port);

var recordanduser = {};
var users = {};
var records = {};
var io = ioServer.listen(server);
io.sockets.on('connection', function (socket) {
    socket.on("change", function (data) {
        var recuser = data["data"];
        var editdetail = data["editdetail"];
        var splitrecuser = recuser.split("##");
        socket.join(splitrecuser[0]);
        socket.roomID = splitrecuser[0];
        socket.userid = splitrecuser[1];
        socket.editdetail = editdetail;
        if(recordanduser[editdetail] == undefined){
            recordanduser[editdetail] = [];
        }
        if(records[editdetail] == undefined){
            records[editdetail] = [];
        }
        if(users[editdetail] == undefined){
            users[editdetail] = [];
        }
        if(!recordanduser[editdetail].includes(recuser)){
          recordanduser[editdetail] = recuser;        
        } 
        var key =  Object.keys(records[editdetail]).filter(function(key) {return records[editdetail][key] === splitrecuser[0]});
       
        var index = key.indexOf(recuser);
        if (index > -1) {
            key.splice(index, 1);
        }
        if(key[0]!==undefined && !users[editdetail].includes(splitrecuser[1]) && !key.includes(recuser)){
            var block = 1;
        } else {
            block = 0;
        }
        records[editdetail][recuser] = splitrecuser[0];
        users[editdetail][recuser] = splitrecuser[1];
        io.sockets.to(splitrecuser[0]).emit("block",block);
    });

    socket.on('disconnect', function(){
                
        var roomID = socket.roomID;
        var userid = socket.userid;
        var editdetail = socket.editdetail;
        var id = roomID+'##'+userid;
        if(recordanduser[editdetail] == undefined){
            recordanduser[editdetail] = [];
        }
        if(records[editdetail] == undefined){
            records[editdetail] = [];
        }
        if(users[editdetail] == undefined){
            users[editdetail] = [];
        }
        recordanduser[editdetail] = Object.keys(records[editdetail]).filter(function(records1) {return records1 !== id});
        users[editdetail].hasOwnProperty(id);
        delete users[editdetail][id];
        users[editdetail].hasOwnProperty(id);
        records[editdetail].hasOwnProperty(id);
        delete records[editdetail][id];
        records[editdetail].hasOwnProperty(id);
        
        var key =  Object.keys(records[editdetail]).filter(function(key) {return records[editdetail][key] === roomID});
       
        var index = key.indexOf(id);
        if (index > -1) {
            key.splice(index, 1);
        }
        if(key.length > 1){
            var block = 1;
        } else {
            block = 0;
        }
        io.sockets.to(roomID).emit("block",block);

    });
});

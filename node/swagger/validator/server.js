#!/usr/bin/env node

var http = require("http");
var requestLib = require('request');
var url = require("url");
var fs = require('fs');
var port = process.argv[2] || 8080;

function start(route) {
    function onRequest(request, response) {
        var query = url.parse(request.url,true).query;
        var pathname = url.parse(request.url).pathname;
        var urlSwagger = query.url;
        console.log("Request for " + pathname + " received.");

        var spec = require('swagger-tools').specs.v2; // Using the latest Swagger 2.x specification

        function startValidate(swaggerObject)
        {
            if (pathname === '/debug') {
                response.writeHead(200, {"Content-Type": "text/html"});
                spec.validate(swaggerObject, function (err, result) {
                    if (err) {
                      throw err;
                    }

                    if (typeof result !== 'undefined') {
                        if (result.errors.length > 0) {
                            response.write('The Swagger document is invalid...');
                            response.write('<br/>');
                            response.write('');
                            response.write('<br/>');
                            response.write('Errors');
                            response.write('<br/>');
                            response.write('------');
                            response.write('<br/>');

                            result.errors.forEach(function (err) {
                              response.write('#/' + err.path.join('/') + ': ' + err.message);
                               response.write('<br/>');
                            });

                            response.write('');
                            response.write('<br/>');
                        }

                        if (result.warnings.length > 0) {
                            response.write('Warnings');
                            response.write('<br/>');
                            response.write('--------');
                            response.write('<br/>');

                            result.warnings.forEach(function (warn) {
                                response.write('#/' + warn.path.join('/') + ': ' + warn.message);
                                response.write('<br/>');
                            });
                        }

                    } else {
                        response.write('Swagger document is valid');
                        response.write('<br/>');
                    }
                    response.end();
                });
            } else {
                spec.validate(swaggerObject, function (err, result) {
                    if (err) {
                      throw err;
                    }

                    if (typeof result !== 'undefined') {
                        var img = fs.readFileSync('./img/error.png');


                    } else {
                        var img = fs.readFileSync('./img/valid.png');
                    }
                    response.writeHead(200, {'Content-Type': 'image/png' });
                    response.end(img, 'binary');
                });
            }
        }

        requestLib(urlSwagger, function (error, response, body) {
            if (!error && response.statusCode == 200) {
                startValidate(JSON.parse(body)); // Show the HTML for the Google homepage.
            } else {
                console.error(error);
            }
        })

    }

    http.createServer(onRequest).listen(port);
    console.log("Port " + port);
    console.log("Server has started.");
}

exports.start = start;

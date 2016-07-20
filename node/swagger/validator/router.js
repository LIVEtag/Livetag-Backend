#!/usr/bin/env node

function route(pathname) {
  console.log("About to route a request for " + pathname);
}

exports.route = route;

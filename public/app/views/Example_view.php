<?php
/**
 * Created by PhpStorm.
 * User: 204072257
 * Date: 4/11/2016
 * Time: 8:24 PM
 */?>
<!Doctype html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js"></script>
        <script src="http://localhost/cf_ui/cf-ui/public/app/controllers/exampleController.js"></script>
    </head>
    <body ng-app="example">

    <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">
                        Hello! this is an example application
                    </a>
                </div>
            </div>
        </nav>

        <section class="container"  ng-controller="default" >
            <div class="row">

                <div class="col-md-12">

                    <div>
                        <h3>Endpoint info</h3>
                        <hr/>
                    </div>

                    <div>
                        <table class="table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Attribute</th>
                                    <th>Value</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr ng-repeat="(key, value) in info" >
                                    <td>{{key}}</td>
                                    <td>{{value}}</td>
                                </tr>
                            </tbody>

                        </table>
                    </div>
                </div>

                <div class="col-md-12">

                    <div>
                        <h3>Login</h3>
                        <hr/>
                    </div>
                    <div>
                        <form class="form-inline">
                            <div class="form-group">
                                <label for="username">User</label>
                                <input type="text" class="form-control" ng-model="username" placeholder="User" id="username" />
                            </div>

                            <div class="form-group">
                                <label for="password">User</label>
                                <input type="password" class="form-control" ng-model="password" id="password" />
                            </div>

                            <button class="btn btn-primary" ng-click="getToken()">Get Token</button><br/>

                        </form><br/>

                        <textarea ng-model="token" cols="50" rows="10" style="resize: none"></textarea>


                    </div>
                </div>

                <div class="col-md-12">

                    <div>
                        <h3>Get apps</h3>
                        <hr/>
                    </div>

                    <button class="btn btn-primary" ng-click="getSpaces()">Get Spaces</button><br/><br/>

                    <select ng-model="spaceguid" id="spaceguid" ng-change="getApps()">
                      <option ng-repeat="space in spaces" value="{{space.metadata.guid}}">{{space.entity.name}}</option>
                    </select>

                    <div class="form-group">
                        <label for="spaceguid">Space Guid</label>
                        <input type="text" class="form-control" ng-model="spaceguid" id="spaceguid" />
                    </div>

                    <form class="form-inline">
                        <div class="form-group">
                            <label for="appname">App Name</label>
                            <input type="text" class="form-control" ng-model="appname" placeholder="Application Name" id="appname" />
                        </div>

                        <button class="btn btn-primary" ng-click="createApp()">Create App</button><br/>

                    </form><br/>

                    <button class="btn btn-primary" ng-click="getApps()">Show Apps</button><br/><br/>
                    <select ng-model="appguid" id="appguid">
                      <option ng-repeat="app in apps" value="{{app.metadata.guid}}">{{app.entity.name}}</option>
                    </select>

                    <form class="form-inline">
                        <label for="appguid">App Guid</label>
                        <input type="text" class="form-control" ng-model="appguid" id="appguid" />
                        <button class="btn btn-primary" ng-click="deleteApp()">Delete App</button><br/>
                        <button class="btn btn-primary" ng-click="updateApp()">Update App</button><br/>
                    </div>

                    <table class="table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Index</th>
                            <th>Name</th>
                            <th>State</th>
                            <th>Version</th>
                            <th>Buildpack</th>
                            <th>Diego</th>
                            <th>Disk quota</th>
                            <th>Enable ssh</th>
                            <th>Events URL</th>
                            <th>Instances</th>
                            <th>Memory</th>
                            <th>Package State</th>
                        </tr>
                        </thead>

                        <tbody>
                        <tr ng-repeat="app in apps" >
                            <td>{{$index}}</td>
                            <td>{{app.entity.name}}</td>
                            <td>{{app.entity.state}}</td>
                            <td>{{app.entity.version}}</td>
                            <td>{{app.entity.buildpack}}</td>
                            <td>{{app.entity.diego}}</td>
                            <td>{{app.entity.disk_quota}}</td>
                            <td>{{app.entity.enable_ssh}}</td>
                            <td>{{app.entity.events_url}}</td>
                            <td>{{app.entity.instances}}</td>
                            <td>{{app.entity.memory}}</td>
                            <td>{{app.entity.package_state}}</td>

                        </tr>
                        </tbody>

                    </table>
                </div>

                <div class="col-md-12">

                    <div>
                        <h3>Services</h3>
                        <hr/>
                    </div>

                    <button class="btn btn-primary" ng-click="getServices()">Get Services</button><br/><br/>

                    <select ng-model="serviceguid" id="serviceguid">
                      <option ng-repeat="service in services" value="{{service.metadata.guid}}">{{service.entity.label}}</option>
                    </select>

                </div>


            </div>
        </section>
    </body>
</html>

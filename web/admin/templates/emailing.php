<h1>Emailing</h1>	
<hr>

<div class="alert alert-info" role="alert">
<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
  Send mail for <strong>{{data.userSelect.length ? data.userSelect.length: 'All'}}</strong> users  {{users.filter.grp != -1 ? 'in group ' + users.filter.grpname : 'from All groups'}}!
 </div>

<div class="dropdown clr padding-5">Group:
	<div class="btn-group">
		<button type="button" class="btn btn-default">{{users.filter.grpname}}</button>
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
		<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu" style="z-index: 1001">
			<li><a href="" ng-click="changeFilter({'grp': '-1'})">All</a></li>
			<li class="divider"></li>
			<li ng-repeat="sub in userFilterList.grp"> <a tabindex="-1" href="" ng-click="changeFilter({'grp': sub.id})">{{sub.name}}</a> </li>
		</ul>
	</div>
</div>
<div class="inline-block">
Users:
	<multiselect style="display: inline-block; width: 250px" ng-model="data.userSelect" show-search="auth.browser!='MXS'" search-limit="300" options="users.users" id-prop="id" show-select-all="true" show-unselect-all="true" id-prop="id" display-prop="user"></multiselect>
</div>

<hr>

Template: 
<div class="btn-group" uib-dropdown is-open="status.isopen">
  <button id="single-button" type="button" class="btn btn-primary" uib-dropdown-toggle ng-disabled="disabled">
  {{data.currenttpl < 0 ? 'None' : data.templates[data.currenttpl]}} 
  <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="single-button">
	<li role="menuitem"><a href="" ng-click="changeTemplate(-1)">None</a></li>
	<li class="divider"></li>
	<li role="menuitem" ng-repeat="tpl in data.templates"><a href="" ng-click="changeTemplate($index)">{{tpl}}</a></li>
  </ul>
</div>
&nbsp;&nbsp;&nbsp;
<label class="checkbox-inline"><input type="checkbox" value="" ng-model="data.force">Force send mail ignore unsubscribing</label>

<br><br>
<form>
	<div class="input-group col-md-12 col-sm-12 col-xs-12 col-md-12">
		<input type="text" class="form-control" placeholder="Subject" aria-describedby="basic-addon1" ng-model="data.subject">  
	</div>
	<br>
	<div class="form-group">
		<textarea class="form-control" rows="20" placeholder="Your Message" id="msg" ng-model="data.content"></textarea>
	</div>
    <div class="btn-group" uib-dropdown dropdown-append-to-body>
      <button id="btn-append-to-body" type="button" class="btn btn-primary" uib-dropdown-toggle>
       <span class="glyphicon glyphicon-pushpin" aria-hidden="true"></span> Attach <span class="caret"></span>
      </button>
      <ul class="dropdown-menu" uib-dropdown-menu role="menu" aria-labelledby="btn-append-to-body">
        <li role="menuitem"><a href="" ng-click="attachToMail('lastassets')">Last Assets</a></li>
	    <li class="divider"></li>
        <li role="menuitem"><a href="" ng-click="attachToMail('img')">Image</a></li>    
        <li role="menuitem" ng-click="attachToMail('favorite')"><a href="">Favorite</a></li>
      </ul>
    </div>
	
	<button type="button" class="btn btn-primary pull-right" ng-click="sendEmail(data)"> Send <span class="glyphicon glyphicon-send" aria-hidden="true"></span></button>
</form>

<div class="col-md-6">
	
</div>
{{userNames}}
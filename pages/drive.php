<!DOCTYPE html>
<!--
    Google Drive Scraper - Scrapes data from Google Drive feed
    Copyright (C) 2014  Sebastian Cross
    Copyright (C) 2014  Zak Waters

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
     <script type="text/javascript" src="build/tincan.js"></script>
    <script type="text/javascript">
           
// Client_ID and API_KEY is unique to each google account.
var CLIENT_ID = '';
var API_KEY = '';
var SCOPES = 'https://www.googleapis.com/auth/drive';

function handleClientLoad() {
    gapi.client.setApiKey(API_KEY);
    window.setTimeout(checkAuth,1);
}

function checkAuth() {
    var options = {
        client_id: CLIENT_ID,
        scope: SCOPES,
        immediate: true
    };
    gapi.auth.authorize(options, handleAuthResult);
}

function handleAuthResult(authResult) {
    var authorizeButton = document.getElementById('authorize-button');

    if (authResult && !authResult.error) {
        authorizeButton.style.visibility = 'hidden';
        makeApiCall();
    } else {
        authorizeButton.style.visibility = '';
        authorizeButton.onclick = handleAuthClick;
    }
}

function handleAuthClick(event) {
    var options = {
        client_id: CLIENT_ID,
        scope: SCOPES,
        immediate: false
    };
    gapi.auth.authorize(options, handleAuthResult);
    return false;
}

function makeApiCall() {  
    gapi.client.load('drive', 'v2', makeRequest);   
}

/**
 * Retrieve a list of all the files that are in the google drive.
 * 
 */
function makeRequest() {
    var request = gapi.client.drive.files.list({'maxResults': 20 });
    request.execute(function(resp) {          
        for (i=0; i<resp.items.length; i++) {
            var title = resp.items[i].title;
            var modiedate = resp.items[i].modifiedDate;
            var lastModifyingUserName = resp.items[i].lastModifyingUserName;
            var embedLink = resp.items[i].embedLink;
            var alternateLink = resp.items[i].alternateLink;
            var documentID = resp.items[i].id;
            retrieveComments(documentID);
            
            var fileInfo = document.createElement('li');
            fileInfo.appendChild(document.createTextNode('Title: ' + title + ' - last modified date: ' + modiedate + ' - modified by: ' + lastModifyingUserName + ' - ID of document ' + documentID));                
            document.getElementById('files').appendChild(fileInfo);   
           
        tincan.sendStatement(
    {
        actor: {
            mbox: title
        },
        verb: {
            id: "http://adlnet.gov/expapi/verbs/attempted"
        },
        target: {
            id: "http://rusticisoftware.github.com/TinCanJS"
        }
         
    }
);
        }
    });    
}
/**
 * Retrieve a list of all the comments that are in the google drive.
 * @param {String} fileId ID of the file to retrieve comments for.
 * @param {Function} callback Function to call when the request is complete.
 */

function retrieveComments(fileId, callback) {
  var request = gapi.client.drive.comments.list({
    'fileId': fileId});
 request.execute(function(resp) {  
    
            if (typeof resp.items !== 'undefined') {
                for (var i = 0; i < resp.items.length; i++){                 
                    var commentId = resp.items[i].commentId;
                    var content = resp.items[i].content;                  
                    var fileComments = document.createElement('li');
                    fileComments.appendChild(document.createTextNode('Coment ID: ' + commentId + ' -    Comment ' + content ));                
                    document.getElementById('comments').appendChild(fileComments);
               
            tincan.sendStatement(
    {
        actor: {
            mbox: commentId
        },
        verb: {
            id: "http://adlnet.gov/expapi/verbs/attempted"
        },
        target: {
            id: "http://rusticisoftware.github.com/TinCanJS"
        }
         
    }
);
    }
            }    
    });    
  //request.execute(callback);
}
    </script>
    <script type="text/javascript" src="https://apis.google.com/js/client.js?onload=handleClientLoad"></script>
  </head>
  <body>
<button id="authorize-button">Authorize</button>
<div id="files">Files:</div>
<div id="comments">Comments:</div>
  </body>
</html>
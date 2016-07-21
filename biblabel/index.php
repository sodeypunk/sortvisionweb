<?php 
    $imagePath = 'image_sets/';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Bib Label v0.1</title>

<style>
body {
    padding: 0px 30px 0px 20px;
}

.tagger-annotation {
    padding : 0 5px;
    vertical-align : middle;
    text-align : center;
    white-space : nowrap;
}
 
/* remove button */
.tagger-annotation-action-remove {
    background-image: url(../images/remove.png);
    cursor: pointer; 
}
 
.tagger-annotation-action-remove:hover, tr:focus {
    border: 1px solid #CCC;
    background-color: #FFF;
}
 
 
.tagger-annotation-action-remove{
    border: 1px solid transparent;
    height: 20px;
    width: 20px;
    overflow: hidden;
    background-color: transparent;
    background-attachment: scroll;
    background-repeat: no-repeat;
    background-position: 1px 1px;
    padding: 0;
    margin: 0;
}

.scrollable {
    overflow: scroll;
    height: 200px;
}

tr.patches-row > td {
    padding-bottom: 5px;
    padding-right: 5px;
}

#actions [type=file] {
    float: left;
    width: 80%;
}

#actions label {
    float: left;
    width: 120px;
}

.image-info-label {
    float: left;
    width: 100px;
}

.image-info-data {
    float: left;
}

#image-status.red {
    color: red;
}

#image-status.green {
    color: #8AC007;
}

#status-border.red {
    border: 2px solid red;
    padding: 20px;
}

#status-border.green {
    border: 2px solid #8AC007;
    padding: 20px;
}

#image-canvas {
    background-image:url('images/779454-1001-0001.jpg');
    background-size: 100px 100px;
    background-repeat: no-repeat;
    position: relative;
}

#patch-canvas {
    background-image:url('images/779454-1001-0001.jpg');
    background-size: 100px 100px;
    background-repeat: no-repeat;
    position: relative;
}

#bib-labels {
    border-color: red;
}

#loadingImage {
    position: fixed;
    left: 50%;
    top: 50%;
    z-index: 100;
}

#top-overlay {
    position: absolute;
    left: 0;
    top: 0;
    background: rgba(0,0,0,.5);
    width: 100%;
    height: 50px;
    z-index: 999;
}

#patches {
    float: left;
    padding-right: 10px;
}

#patches-listbox {
    width: 300px;
}

.checkmark {
    height: 20px;
    width: 20px;
    display: none;
}

.greenSelect {
    color: green;
}

.yellowBackgroundSelect {
    background-color: yellow;
}

</style>

<link href="css/bootstrap.min.css" rel="stylesheet">

<script src="js/jquery-1.11.3.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</head>
<body>
<div class="row">
    <div id="actions">
        <div class="col-md-2">
            <label for="file-load-label">Races: </label>
            <?php 
                $results = scandir($imagePath);

                echo '<select id="image-set">';
                foreach ($results as $result) {
                    if ($result === '.' or $result === '..') continue;

                    if (is_dir($imagePath . $result)) {
                        echo '<option value="' . $result . '">' . $result . '</option>';
                    }
                }
                echo '</select>';
            ?>
        </div>
        <div class="col-md-5">
            <label for="file-load-label">Labels (optional): </label>
            <input type="file" id="file-label-upload"/>
        </div>
        <div class="col-md-2">
            <input type="checkbox" id="load-drawn-csv"> Load Drawn Bibs?
        </div>
        <div class="col-md-2">
            <input type="button" id="load" value="Load" />
        </div>
    </div>
</div>
<div id="status-border" class="red">
<div class="row">
    <div class="col-md-12">
        <b>Save File:</b>
            <input type="radio" name="save-type" value="patch"/> Patches
            <input type="radio" name="save-type" value="label"/> Labels
            <input type="radio" name="save-type" value="newpatch"/> New Patches
           <input type="text" id="new-file-save" placeholder="filename.csv"/>
           <button id="save-csv-button">Save to CSV</button>
    </div>
</div>
<hr />
<div class="row">
    <div class="col-md-12">
        <label class="image-info-label">Image: </label>
        <div id="image-name" class="image-info-data"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label class="image-info-label">Is Done: </label>
        <div id="image-status" class="red"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label class="image-info-label">Progress: </label>
        <div id="progess"><span id="progress-percent"> 0</span>%, <span id="images-complete"> 0 </span> of <span id="images-total">0</span> images, <span id="bibs-complete"> 0 </span> of <span id="bibs-total">0</span> patches</div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <label class="image-info-label">New Bibs: </label>
        <div id="newbibs-count">0</div>
    </div>
</div>
<hr />
<b>Drawing Tools</b>
<div class="row">
    <div class="col-md-12">
        <button id="clear-drawings-button">Clear Drawings</button>
        <button id="delete-selected-drawing-button">Delete Selected Drawing</button>
        <input type="checkbox" id="use-ratio" checked> Use 2:3 Ratio
    </div>
</div>
<b>Navigation</b>
<div class="row">
    <div class="col-md-12">
        <!--<button  id="viewTags">View Tags</button>
        <button id="tagPhotos">Add Tags</button> -->
        <button id="prev-image-button">Prev Image</button>
        <button id="next-image-button">Next Image</button>
        <button id="done-image-button">Mark Done</button>
        <button id="done-next-image-button">Mark Done, Next Image</button>
        <button id="not-done-image-button">Not Done</button>
        <button id="not-done-next-image-button">Not Done, Next Image</button>
        <input type="text" id="image-load-name" placeholder="779460-1007-0010"/>
        <button id="image-go-button">Go to Image</button>
        
    </div>
</div>
<b>Image Information</b>
<div class="row">
    <div class="col-md-12">
        Labels: <input type="text" id="bib-labels" placeholder="None"/>
        <img src="images/checkmark.png" id="label-checkmark" class="checkmark">
        <button id="save-labels-db-button">Save Labels</button>&nbsp;&nbsp;
        Correct: <b><span id="image-info-percent">-1</span>%</b>&nbsp;&nbsp;
        False Pos: <b><span id="image-info-falsepos">-1</span></b>&nbsp;&nbsp;
        Looking For: <b><span id="image-info-source">-1</span></b>&nbsp;&nbsp;
        Found: <b><span id="image-info-result">-1</span></b>
    </div>
</div>
</br>
Last Exported: <b><span id="image-info-patch-updt">Never</span></b>
</br>
<button id="export-patches-db-button">Export Current Patches For Training</button>
        <input type="checkbox" id="export-updated-bibs-checkbox" checked> Only Updated and -1 Bibs
</br>
</br>
Files Exported (<span id="files-fixed-count">0</span> / <span id="files-fixed-total">0</span>)
&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="load-only-cleanup-images"> Load only cleanup images
</br>
<div id="patches">
    <select id="patches-listbox" size="10">
    </select>
</div>
<div class="row scrollable">
    <table id="patch-table">
        <tbody>
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-md-6">
        <div id="image-space">
            <div id="top-overlay"></div>
            <canvas id="image-canvas" width=600px height=600px></canvas>
        </div>
    </div>
    <div class="col-md-6">
         <canvas id="patch-canvas" width=600px height=600px></canvas>
    </div>
</div>
</div>
<div class="row">
    <div class="col-md-12">
        CSV output:
        <div id="csv">
        </div>
    </div>
</div>

<script>

var IMAGE_EXT = ".jpg";
var IMAGE_WIDTH = 425;
var IMAGE_HEIGHT = 640;
var SOURCE_IMAGE_WIDTH = 425;
var SOURCE_IMAGE_HEIGHT = 640;

var state = null;

$( document ).ready(function() {

    function escapeRegExp(str) 
    {
        if (str != null)
        {
            return str.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
        }
        return null;
    }

    function replaceAll(str, find, replace) 
    {
        if (str != null)
        {
            return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
        }
        return null;
    }

    function timeSince(date) 
    {
        date.setHours(date.getHours() + 2);
        var seconds = Math.floor((new Date() - date) / 1000);

        var interval = Math.floor(seconds / 31536000);

        if (interval > 1) {
            return interval + " years";
        }
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) {
            return interval + " months";
        }
        interval = Math.floor(seconds / 86400);
        if (interval > 1) {
            return interval + " days";
        }
        interval = Math.floor(seconds / 3600);
        if (interval > 1) {
            return interval + " hours";
        }
        interval = Math.floor(seconds / 60);
        if (interval > 1) {
            return interval + " minutes";
        }
        return Math.floor(seconds) + " seconds";
    }

    function Bib() {
        this.strokeColor = "red";
        this.strokeWidth = 2;
        this.labelHeight = 30;
        this.isDone = "false";
        this.patchImagePath = "";
        this.isFound = "";
        this.patchLabel = "";
        this.patchScore1 = 0;
        this.patchScore2 = 0;
        this.patchScore3 = 0;
        this.patchScore4 = 0;
        this.patchScore5 = 0;
        this.patchScore6 = 0;
        this.isCorrect = "";
        this.patchX = 0;
        this.patchY = 0;
        this.patchX2 = 0;
        this.patchY2 = 0;
        this.resizedImageW = 0;
        this.resizedImageH = 0;
        this.mainPatchImagePath = "";
        this.mainLabel = "Undefined";
        this.mainX = 0;
        this.mainY = 0;
        this.mainX2 = 0;
        this.mainY2 = 0;
        this.mainImageKey = "";
        this.committee = 0
        this.labelUpdated = false;
        
        this.patchW = 0;
        this.patchH = 0;
        this.patchLabelX = 0;
        this.patchLabelY = 0;
        
        this.mainW = 0;
        this.mainH = 0;
        this.mainLabelX = 0;
        this.mainLabelY = 0;


    }
    
    function NewBib() {
    	this.lineWidth = 5;
    	this.strokeStyle = 'yellow';
    	this.selected = false;
    	this.startX = 0;
    	this.startY = 0;
    	this.w = 0;
    	this.h = 0;
    	this.sourceImage = '';
    	this.sourceImageW = 0;
    	this.sourceImageH = 0;
    	this.label = "NA";
    }
    
    function LoadDetailedCSV(data, state, rowsToRead) {
    	var table = $("<table border='1'/>");
        console.log("splitting text up")
        var rows = data.split("\n");
        state.bibsTotal = rows.length;
        state.bibsDone = 0;
        console.log("Creating CSV output table...");
        
        if (rowsToRead == null) rowsToRead = rows.length;
        
        for (var i = 0; i < rowsToRead; i++) {
            
            var cells = rows[i].split(",");
            var loadDrawnCSV = $("#load-drawn-csv").prop('checked');
            
            if (loadDrawnCSV)
            {
                var rowKey = cells[0].replace(IMAGE_EXT, "");
                
                var drawnBib = new NewBib();
                drawnBib.sourceImage = rowKey;
                drawnBib.label = cells[1];
                drawnBib.startX = cells[2];
                drawnBib.startY = cells[3];
                drawnBib.w = cells[4] - drawnBib.startX;
                drawnBib.h = cells[5] - drawnBib.startY;
                drawnBib.sourceImageW = cells[6];
                drawnBib.sourceImageH = cells[7];
                
                if ((rowKey in state.newBibsDict) == false)
                {
                    state.newBibsDict[rowKey] = [];
                    state.newBibsDict[rowKey].push(drawnBib);
                    
                    state.newBibsDictCount = state.newBibsDictCount + 1;
                    $('#newbibs-count').html(state.newBibsDictCount);
                    console.log("Loading drawnBib: " + drawnBib.sourceImage +", total: " + state.newBibsDictCount);
                    
                }
                else
                {
                    state.newBibsDict[rowKey].push(drawnBib);
                    
                    state.newBibsDictCount = state.newBibsDictCount + 1;
                    $('#newbibs-count').html(state.newBibsDictCount);
                    console.log("Loading drawnBib: " + drawnBib.sourceImage +", total: " + state.newBibsDictCount);
                }
                
            }
            else
            {
                var rowKey = String(cells[22]).trim();
                
                var newBib = new Bib();
                newBib.patchImagePath = cells[0];
                newBib.isFound = cells[1];
                newBib.patchLabel = cells[2];
                newBib.patchScore1 = cells[3];
                newBib.patchScore2 = cells[4];
                newBib.patchScore3 = cells[5];
                newBib.patchScore4 = cells[6];
                newBib.patchScore5 = cells[7];
                newBib.patchScore6 = cells[8];
                newBib.isCorrect = cells[9];
                newBib.patchX = parseInt(cells[10]);
                newBib.patchY = parseInt(cells[11]);
                newBib.patchX2 = parseInt(cells[12]);
                newBib.patchY2 = parseInt(cells[13]);
                newBib.resizedImageW = cells[14];
                newBib.resizedImageH = cells[15];
                newBib.mainPatchImagePath = cells[16];
                newBib.mainLabel = cells[17];
                newBib.mainX = parseInt(cells[18]);
                newBib.mainY = parseInt(cells[19]);
                newBib.mainX2 = parseInt(cells[20]);
                newBib.mainY2 = parseInt(cells[21]);
                newBib.mainImageKey = cells[22];
                newBib.committee = cells[23]
                newBib.isDone = cells[24] == null ? "false" : cells[24];
                
                if (newBib.isDone == "true")
                {
                    state.bibsDone = state.bibsDone + 1;
                }
                else if (state.bibIndex == -1)
                {
                    // set the bibindex based on how many image keys have been pushed
                    state.bibIndex = state.bibsKey.length;
                }
                    
                
                newBib.patchW = newBib.patchX2 - newBib.patchX;
                newBib.patchH = newBib.patchY2 - newBib.patchY;
                newBib.patchLabelX = newBib.patchX;
                newBib.patchLabelY = newBib.patchY + newBib.patchH + 40;
                
                newBib.mainW = newBib.mainX2 - newBib.mainX;
                newBib.mainH = newBib.mainY2 - newBib.mainY;
                newBib.mainLabelX = newBib.mainX;
                newBib.mainLabelY = newBib.mainY + newBib.mainH + 40;

                if ((rowKey in state.bibsDict) == false)
                {
                    state.bibsDict[rowKey] = [];
                    state.bibsDict[rowKey].push(newBib);
                    state.bibsKey.push(rowKey);
                    if (newBib.isDone == "true") {
                        state.imagesDone = state.imagesDone + 1;
                    }
                    
                    // also add the width and height to store for when saving new bibs
                    var widthHeight = [newBib.resizedImageW, newBib.resizedImageH]
                    state.bibsToImageWHDict[rowKey] = [];
                    state.bibsToImageWHDict[rowKey].push(widthHeight);
                }
                else
                {
                    state.bibsDict[rowKey].push(newBib);
                }
                
                if (newBib.mainLabel != "-1")
                {
                	if ((rowKey in state.bibsLabelDict) == false)
                    {
                		state.bibsLabelDict[rowKey] = [];
                		state.bibsLabelDict[rowKey].push(newBib.mainLabel);
                    }
                	else if (state.bibsLabelDict[rowKey].indexOf(newBib.mainLabel) == -1)
                	{
                		state.bibsLabelDict[rowKey].push(newBib.mainLabel);
                	}
                }
            }

            // Web output
            //var row = $("<tr />");

            //for (var j = 0; j < cells.length; j++) 
            //{
            //    var cell = $("<td />");
            //    cell.html(cells[j]);
            //    row.append(cell);
            //}
            // table.append(row);
        }
        
        console.log("Drawing state");
        if (state.bibIndex == -1) state.bibIndex = 0;
        state.imagesTotal = state.bibsKey.length;
        state.bibsKey.sort();
        state.loadBibs(state.bibIndex);
        state.valid = false; // redraw first image
        state.draw();
        updateProgress();
        
        // Web output
        $("#csv").html('');
        $("#csv").append(table);
        console.log("Done Creating CSV output table.");
    	
    }

    // Draws this shape to a given context
    Bib.prototype.draw = function(imageContext, patchContext) {
    	
    	if (imageContext != null)
    	{
            // Draw main bib
            imageContext.beginPath();
            imageContext.lineWidth=this.strokeWidth;
            imageContext.strokeStyle=this.strokeColor;
            imageContext.rect(this.mainX, this.mainY, this.mainW, this.mainH);
            imageContext.stroke();
    
            imageContext.fillStyle = 'black';
            imageContext.fillRect(this.mainLabelX, this.mainLabelY - 35, imageContext.measureText(this.mainLabel).width + 30, this.labelHeight);
    
            imageContext.font = "10pt Arial";
            imageContext.fillStyle = 'white';
            imageContext.fillText(this.mainLabel + "(" + String(this.committee).trim() + ")", this.mainLabelX, this.mainLabelY - 13);
    	}
    	
        if (patchContext != null)
        {
            // Draw patch bib
            patchContext.beginPath();
            patchContext.lineWidth=2;
            patchContext.strokeStyle=this.strokeColor;
            patchContext.rect(this.patchX, this.patchY, this.patchW, this.patchH);
            patchContext.stroke();
    
            patchContext.fillStyle = 'black';
            patchContext.fillRect(this.patchLabelX, this.patchLabelY - 35, patchContext.measureText(this.patchLabel).width, 20);
    
            patchContext.font = "10pt Arial";
            patchContext.fillStyle = 'white';
            patchContext.fillText(this.patchLabel, this.patchLabelX, this.patchLabelY - 15);
        }
    }

    // Determine if a point is inside the shape's bounds
    Bib.prototype.contains = function(mx, my) {
      // All we have to do is make sure the Mouse X,Y fall in the area between
      // the shape's X and (X + Width) and its Y and (Y + Height)
      var greaterThanX = this.mainX <= mx;
      var greaterThanY = this.mainY <= my;
      var lessThanW = (this.mainX + this.mainW) >= mx;
      var lessThanH = (this.mainY + this.mainH) >= my;
      //console.log("bib x: " + this.x + ", bib y: " + this.y + ", bib width: " + this.w + ", bib height: " + this.h);
      //console.log("greaterThanX: " + greaterThanX + ", greaterThanY: " + greaterThanY + ", lessThanW: " + lessThanW + ", lessThanH: " + lessThanH);
      return  greaterThanX && greaterThanY &&
              lessThanW && lessThanH;
    }

    function CanvasState(imageCanvas, patchCanvas) 
    {
        // **** First some setup! ****
        this.imageCanvas = imageCanvas;
        this.patchCanvas = patchCanvas;
        this.width = imageCanvas.width;
        this.height = imageCanvas.height;
        this.imageContext = imageCanvas.getContext('2d');
        this.patchContext = patchCanvas.getContext('2d');;
        // This complicates things a little but but fixes mouse co-ordinate problems
        // when there's a border or padding. See getMouse for more detail
        var stylePaddingLeft, stylePaddingTop, styleBorderLeft, styleBorderTop;
        if (document.defaultView && document.defaultView.getComputedStyle) 
        {
            this.stylePaddingLeft = parseInt(document.defaultView.getComputedStyle(imageCanvas, null)['paddingLeft'], 10)      || 0;
            this.stylePaddingTop  = parseInt(document.defaultView.getComputedStyle(imageCanvas, null)['paddingTop'], 10)       || 0;
            this.styleBorderLeft  = parseInt(document.defaultView.getComputedStyle(imageCanvas, null)['borderLeftWidth'], 10)  || 0;
            this.styleBorderTop   = parseInt(document.defaultView.getComputedStyle(imageCanvas, null)['borderTopWidth'], 10)   || 0;
        }
        // Some pages have fixed-position bars (like the stumbleupon bar) at the top or left of the page
        // They will mess up mouse coordinates and this fixes that
        var html = document.body.parentNode;
        this.htmlTop = html.offsetTop;
        this.htmlLeft = html.offsetLeft;

        // **** Keep track of state! ****
        this.imagePath = "";
        this.patchPath = "";
        this.bibsTotal = 0;
        this.bibsDone = 0;
        this.imagesDone = 0;
        this.imagesTotal = 0;
        this.valid = false; // when set to false, the canvas will redraw everything
        this.bibs = [];
        this.currentBib = {};
        this.bibIndex = -1;
        this.bibsDict = {};
        this.bibsLabelDict = {};
        this.newBibsDict = {};
        this.bibsToImageWHDict = {};
        this.bibsKey = [];
        this.patchesKey = [];
        this.dragging = false;
        this.scaleX = 0;
        this.scaleY = 0;
        this.newBibsDictCount = 0;
        this.useRatio = $("#use-ratio").prop('checked');
        this.selectedDrawing = null;
        this.draggingExisting = false;
        this.dragX = 0;
        this.dragY = 0;

        // **** Then events! ****

        // This is an example of a closure!
        // Right here "this" means the CanvasState. But we are making events on the Canvas itself,
        // and when the events are fired on the canvas the variable "this" is going to mean the canvas!
        // Since we still want to use this particular CanvasState in the events we have to save a reference to it.
        // This is our reference!
        var state = this;
          
        //fixes a problem where double clicking causes text to get selected on the canvas
        imageCanvas.addEventListener('selectstart', function(e) 
        { 
            e.preventDefault(); 
            return false; 
        }, false);
        
        imageCanvas.addEventListener('contextmenu', function(e) {
        	e.preventDefault(); 
            return false; 
        }, false);
        
        // Up, down, and move are for dragging
        imageCanvas.addEventListener('mousedown', function(e) 
        {
        	var left = 0;  //equal to 1 in IE?
        	var right = 2;
        	state.useRatio = $("#use-ratio").prop('checked');
        	var mouse = state.getMouse(e);
            var mouseX = mouse.x / state.scaleX;
            var mouseY = mouse.y / state.scaleY;
            
        	if (e.button === left)
        	{
        		if (state.selectedDrawing != null)
        		{
        			if ((mouseX >= state.selectedDrawing.startX && mouseX <= (state.selectedDrawing.startX + state.selectedDrawing.w)) &&
                            (mouseY >= state.selectedDrawing.startY && mouseY <= (state.selectedDrawing.startY + state.selectedDrawing.h)))
        			{
        				state.dragX = mouseX;
        				state.dragY = mouseY;
        				state.draggingExisting = true;
        				console.log("Selected Existing Drawing");
        			}
        		}
        		
        		if (state.draggingExisting == false)
        		{
            		state.currentBib = new NewBib();
                	var mouse = state.getMouse(e);
                	state.currentBib.startX = mouseX;
                	state.currentBib.startY = mouseY;
                	state.dragging = true;
                	console.log("Mouse is down at " + state.currentBib.startX + ", " + state.currentBib.startY);
        		}
        	}
        	else if (e.button === right)
        	{
        		if (state.selectedDrawing != null)
                {
        			  var label = prompt("Please enter label for this box: ");
        			  state.selectedDrawing.label = label;
        			  state.imageContext.clearRect(0,0,imageCanvas.width / state.scaleX, imageCanvas.height / state.scaleY);
        			  state.drawNewBibs();
                      state.drawExistingBibs();
                }
        		
        	}
        }, false);

        imageCanvas.addEventListener('mousemove', function(e) 
        {
        	var mouse = state.getMouse(e);
            var mouseX = mouse.x / state.scaleX;
            var mouseY = mouse.y / state.scaleY;
            
        	if (state.draggingExisting == true)
        	{
        		var dragXDifference = mouseX - state.dragX;
        		var dragYDifference = mouseY - state.dragY;
        		
        		state.imageContext.clearRect(0,0,imageCanvas.width / state.scaleX, imageCanvas.height / state.scaleY);
                state.imageContext.beginPath();
                state.imageContext.lineWidth = state.selectedDrawing.lineWidth;
                state.imageContext.strokeStyle = state.selectedDrawing.strokeStyle;
                state.imageContext.rect(state.selectedDrawing.startX + dragXDifference, state.selectedDrawing.startY + dragYDifference, state.selectedDrawing.w, state.selectedDrawing.h);
                state.imageContext.stroke();
                state.drawNewBibs();
                state.drawExistingBibs();
                console.log("Dragging Existing Drawing");
        	}
        	else if (state.dragging){
            	
            	state.currentBib.h = mouseY - state.currentBib.startY;
            	// force ratio
            	if (state.useRatio)
            	{
            		   state.currentBib.w = state.currentBib.h * 1.5;
            	}
            	else
           		{
            		state.currentBib.w = mouseX - state.currentBib.startX;
           		}
            	
            	state.imageContext.clearRect(0,0,imageCanvas.width / state.scaleX, imageCanvas.height / state.scaleY);
            	state.imageContext.beginPath();
            	state.imageContext.lineWidth = state.currentBib.lineWidth;
            	state.imageContext.strokeStyle = state.currentBib.strokeStyle;
            	state.imageContext.rect(state.currentBib.startX, state.currentBib.startY, state.currentBib.w, state.currentBib.h);
            	state.imageContext.stroke();
            	state.drawNewBibs();
            	state.drawExistingBibs();
            	//console.log("Redrawing from " + state.currentBib.startX + ", " + state.currentBib.startY + " with width and height of " + state.currentBib.w + ", " + state.currentBib.h);
            }
        }, false);

        imageCanvas.addEventListener('mouseup', function(e) 
        {
        	if (state.draggingExisting == true)
        	{
        		var mouse = state.getMouse(e);
                var mouseX = mouse.x / state.scaleX;
                var mouseY = mouse.y / state.scaleY;
                
                var dragXDifference = mouseX - state.dragX;
                var dragYDifference = mouseY - state.dragY;
                
                state.selectedDrawing.startX = state.selectedDrawing.startX + dragXDifference;
                state.selectedDrawing.startY = state.selectedDrawing.startY + dragYDifference;
                
        		console.log("Mouse is up");
                state.draggingExisting = false;
                
                state.imageContext.clearRect(0,0,imageCanvas.width / state.scaleX, imageCanvas.height / state.scaleY);
                state.drawNewBibs();
                state.drawExistingBibs();
        	}
        	if (state.dragging == true)
        	{
        		console.log("Mouse is up");
        	    state.dragging = false;
        	    
        	    if (state.currentBib.w > 10 && state.currentBib.h > 10)
        	    {
        	    	   var sourceImage = state.bibsKey[state.bibIndex];
        	    	   
        	    	   if (sourceImage !== undefined)
        	    	   {
        	    		   state.currentBib.sourceImage = sourceImage;
        	    		   state.currentBib.sourceImageW = state.bibsToImageWHDict[sourceImage][0][0];
        	    		   state.currentBib.sourceImageH = state.bibsToImageWHDict[sourceImage][0][1];
        	    	   }
        	    	   else
       	    		   {
        	    		   sourceImage = 'noimage';
       	    		   }
        	    	   
        	    	   if ((sourceImage in state.newBibsDict) == false)
                       {
        	    		   state.newBibsDict[sourceImage] = [];
        	    		   state.newBibsDict[sourceImage].push(state.currentBib);
                           
                       }
                       else
                       {
                    	   state.newBibsDict[sourceImage].push(state.currentBib);
                       }
        	           
        	    	   state.newBibsDictCount = state.newBibsDictCount + 1;
        	    	   $('#newbibs-count').html(state.newBibsDictCount);
        	    	   console.log("Saving new bib for image: " + sourceImage + " w: " + state.currentBib.sourceImageW +  " h: " + state.currentBib.sourceImageH + ", total: " + state.newBibsDictCount);
        	    }
        	}
        }, false);
        
        // double click for making new bibs
        imageCanvas.addEventListener('dblclick', function(e) 
        {
        	var mouse = state.getMouse(e);
            var mouseX = mouse.x / state.scaleX;
            var mouseY = mouse.y / state.scaleY;
            
            var sourceImage = state.bibsKey[state.bibIndex];
            if (sourceImage == undefined)
            {
                sourceImage = 'noimage';
            }
            if (sourceImage in state.newBibsDict)
            {
                var newBibs = state.newBibsDict[sourceImage];
                for (var i=0; i<newBibs.length; i++)
               	{
                    if ((mouseX >= newBibs[i].startX && mouseX <= (newBibs[i].startX + newBibs[i].w)) &&
                    	(mouseY >= newBibs[i].startY && mouseY <= (newBibs[i].startY + newBibs[i].h)))
                    {
                    	if (newBibs[i].selected == true)
                    	{
                    		newBibs[i].strokeStyle='yellow';
                            newBibs[i].selected = false;
                            state.selectedDrawing = null;
                            break;
                    	}
                    	else
                    	{
                            state.resetNewBibStyles();
                        	newBibs[i].strokeStyle='blue';
                        	newBibs[i].selected = true;
                        	state.selectedDrawing = newBibs[i];
                        	break;
                    	}
                    }
               	}
            }

            state.imageContext.clearRect(0,0,imageCanvas.width / state.scaleX, imageCanvas.height / state.scaleY);
            state.drawNewBibs();
            state.drawExistingBibs();
        	console.log("Mouse dblclick");
        	return false;
            
        }, false);
        
    }

    CanvasState.prototype.clearAllObjects = function ()
    {
        state.imagePath = "";
        state.patchPath = "";
        state.bibsTotal = 0;
        state.bibsDone = 0;
        state.imagesDone = 0;
        state.imagesTotal = 0;
        state.valid = false; // when set to false, the canvas will redraw everything
        state.bibs = [];
        state.currentBib = {};
        state.bibIndex = -1;
        state.bibsDict = {};
        state.bibsLabelDict = {};
        state.newBibsDict = {};
        state.bibsToImageWHDict = {};
        state.bibsKey = [];
        state.patchesKey = [];
        state.dragging = false;
        state.scaleX = 0;
        state.scaleY = 0;
        state.newBibsDictCount = 0;
        state.useRatio = $("#use-ratio").prop('checked');
        state.selectedDrawing = null;
        state.draggingExisting = false;
        state.dragX = 0;
        state.dragY = 0;
    }
    
    CanvasState.prototype.deleteSelectedDrawings = function()
    {
    	state.imageContext.clearRect(0,0,state.imageCanvas.width / state.scaleX, state.imageCanvas.height / state.scaleY);
    	var indexToDelete = -1;
    	
    	var sourceImage = state.bibsKey[state.bibIndex];
        if (sourceImage == undefined)
        {
            sourceImage = 'noimage';
        }
        if (sourceImage in state.newBibsDict)
        {
            var newBibs = state.newBibsDict[sourceImage];
            for (var i=0; i<newBibs.length; i++)
            {
                if (newBibs[i].selected == true)
                {
                    indexToDelete = i;
                    console.log("index to delete: " + i);
                    break;
                }
            }
        
            if (indexToDelete >= 0)
            {
            	state.newBibsDict[sourceImage].splice(indexToDelete, 1);
            }
        }
        state.drawNewBibs();
        state.drawExistingBibs();
        state.newBibsDictCount = state.newBibsDictCount - 1;
        $('#newbibs-count').html(state.newBibsDictCount);
    }
    
    // Draws all new bib shapes that belong to the current image
    CanvasState.prototype.drawNewBibs = function()
    {
    	var sourceImage = state.bibsKey[state.bibIndex];
    	if (sourceImage == undefined)
        {
    		sourceImage = 'noimage';
        }
    	if (sourceImage in state.newBibsDict)
    	{
    		var newBibs = state.newBibsDict[sourceImage];
    		for (var i=0; i<newBibs.length; i++)
    		{
    			state.imageContext.beginPath();
    			state.imageContext.lineWidth = newBibs[i].lineWidth;
    			state.imageContext.strokeStyle = newBibs[i].strokeStyle;
    			state.imageContext.rect(newBibs[i].startX, newBibs[i].startY, newBibs[i].w, newBibs[i].h);
    			state.imageContext.stroke();
    			
    			// Draw labels if it has it
                if (newBibs[i].label != "NA")
                {
                	state.imageContext.fillStyle = 'black';
                	state.imageContext.fillRect(newBibs[i].startX, newBibs[i].startY + newBibs[i].h + 8, state.imageContext.measureText(newBibs[i].label).width + 10, 25);
              
                	state.imageContext.font = "15pt Arial";
                	state.imageContext.fillStyle = 'white';
                	state.imageContext.fillText(newBibs[i].label, newBibs[i].startX, newBibs[i].startY + newBibs[i].h + 25);
                }
            }
    	}
    }
    
    // Draws all bib detections that were loaded from the excel spreadsheet
    CanvasState.prototype.drawExistingBibs = function()
    {
        for (var i=0; i<state.bibs.length; i++)
        {
            state.bibs[i].draw(state.imageContext, null);
        }
    }
    
    CanvasState.prototype.resetNewBibStyles = function()
    {
       state.selectedDrawing = null;
	   for (var key in state.newBibsDict)
	   {
            var newBibs = state.newBibsDict[key];
            for (var i=0; i<newBibs.length; i++)
            {
            	newBibs[i].lineWidth = 5;
            	newBibs[i].strokeStyle = 'yellow';
            	newBibs[i].selected = false;
            }
        }
    }
    
    CanvasState.prototype.CheckForUnlabeledBibs = function()
    {
       console.log("Checking for unlabeled bibs.");
       for (var key in state.newBibsDict)
       {
            var newBibs = state.newBibsDict[key];
            for (var i=0; i<newBibs.length; i++)
            {
                if (newBibs[i].label == "NA")
                {
                	alert("Not all new bibs have a label!");
                	return false;
                }
            }
        }
    }

    CanvasState.prototype.clearNewRects = function() 
    {
        state.imageContext.clearRect(0,0,state.imageCanvas.width / state.scaleX, state.imageCanvas.height / state.scaleY);
        
        var sourceImage = state.bibsKey[state.bibIndex];
        if (sourceImage == undefined)
        {
            sourceImage = 'noimage';
        }
        if (sourceImage in state.newBibsDict)
        {
           state.newBibsDict[sourceImage] = [];
           state.drawExistingBibs();
        }
    }
    CanvasState.prototype.draw = function() 
    {
    		
        console.log("Calling draw bibs...");
        // if our state is invalid, redraw and validate!
        if (!this.valid) {

            // Draw background image
            console.log("Loading image: " + this.bibImagePath);
            if (this.bibImagePath == undefined) return;
            $("#image-name").html(this.bibImagePath);

            var image = new Image();
            image.src = this.bibImagePath;
            
            image.onload = (function() 
            {
            	resizeFactor = 1.2;
                var canvasWidth = IMAGE_WIDTH * resizeFactor;
                var canvasHeight = IMAGE_HEIGHT * resizeFactor;
                var scaleX = canvasWidth/SOURCE_IMAGE_WIDTH;
                var scaleY = canvasHeight/SOURCE_IMAGE_HEIGHT;
                if (image.width > image.height)
                {
                	var canvasWidthTemp = canvasWidth;
                    canvasWidth = canvasHeight;
                    canvasHeight = canvasWidthTemp;
                    scaleX = canvasWidth/SOURCE_IMAGE_HEIGHT;
                    scaleY = canvasHeight/SOURCE_IMAGE_WIDTH;
                }
                
                state.scaleX = scaleX;
                state.scaleY = scaleY;
                
                // Draw background for image
                state.imageContext.clearRect(0, 0, state.imageCanvas.width / state.scaleX, state.imageCanvas.height / state.scaleY);
                state.imageCanvas.width = canvasWidth;
                state.imageCanvas.height = canvasHeight;
                //state.imageContext.drawImage(image, 0, 0, canvasWidth, canvasHeight); // Draw image only after loading is finished
                // Draw background for patches
                state.patchCanvas.width = canvasWidth;
                state.patchCanvas.height = canvasHeight;
                //state.patchContext.drawImage(image, 0, 0, canvasWidth, canvasHeight); // Draw image only after loading is finished
                
                console.log("Loading into background: " + state.bibImagePath + " with: " + canvasWidth + " height: " + canvasHeight)
                $('#image-canvas').css('background-image', 'url(' + state.bibImagePath + ')');
                $('#image-canvas').css('background-size',canvasWidth + 'px ' + canvasHeight + 'px');
                
                $('#patch-canvas').css('background-image', 'url(' + state.bibImagePath + ')');
                $('#patch-canvas').css('background-size',canvasWidth + 'px ' + canvasHeight + 'px');

                // Set the top overlay
                $("#top-overlay").css("height", (canvasHeight * 0.25) + "px")
                
                // Set scales for drawing the bibs
                state.imageContext.scale(scaleX, scaleY);
                state.patchContext.scale(scaleX, scaleY);
                
                // Get info from server
                var key = state.bibsKey[state.bibIndex];
                var imageInfo = JSON.parse(GetImageInfoFromDB(key));
                var patchesDB = imageInfo[0]['PATCHES'];
                var isDone = imageInfo[0]['DONE'];

                // draw all bibs
                var l = state.bibs.length;

                var tbody = $("#patch-table tbody");
                tbody.html(""); // clear the body
                var html = "";
                for (var i = 0; i < l; i++) {
                    var shape = state.bibs[i];
                    $("#image-status").html(isDone == "1");
                    if (isDone == "0" || isDone == null) {
                    	$("#image-status").attr("class", "red");
                    	$("#status-border").attr("class", "red");
                    }else if (isDone == "1") {
                        $("#image-status").attr("class", "green");
                        $("#status-border").attr("class", "green");
                    }
                    state.bibs[i].draw(state.imageContext, state.patchContext);
                    
                    // Draw patches table
                    var mainLabel = "";
                    var color = "black";
                    var patchFileName = state.bibs[i].patchImagePath;

                    if (patchesDB[patchFileName] != null && (patchesDB[patchFileName] != state.bibs[i].mainLabel || patchesDB[patchFileName] == "-1" || state.bibs[i].labelUpdated))
                    {
                        mainLabel = patchesDB[patchFileName];
                        color = "green";
                    }
                    else
                    {
                        mainLabel = state.bibs[i].mainLabel;
                    }
                    
                    if (i % 9 == 0)
                    {
                    	html += "<tr class='patches-row'>";
                    }
                    
                    var patchImagePathClean = state.bibs[i].patchImagePath.replace(IMAGE_EXT, "");
                    var imagePatchPath = state.patchPath;
                    html += "<td><img src='" + imagePatchPath + "/" + patchFileName + "'></td><td><input size='5' type='text' id='" + patchImagePathClean + "' value='" + mainLabel + "' style='color :" + color + "'></td>";
                    
                    if (i !== 0 && (i+1) % 9 == 0)
                    {
                    	html += "</tr>";
                    }
                }
                               
                var labels = imageInfo[0]['LABELS'];
                var found = imageInfo[0]['FOUND'];
                var total = imageInfo[0]['TOTAL'];
                var percentage = imageInfo[0]['PERCENTAGE'];
                var falsePos = imageInfo[0]['FALSE_POS'];
                var source = replaceAll(imageInfo[0]['SOURCE'],"*", ",");
                var result = replaceAll(imageInfo[0]['RESULT'],"*", ",");
                var compareList = imageInfo[0]['COMPARE_LIST'];

                // Last Patches Export Time
                var patchUpDt = null;
                if (imageInfo[0]['PATCH_UPDT'] == null)
                {
                    patchUpDt = "Never";
                    $("#image-info-patch-updt").css("color", "red");
                }
                else
                {
                    var myDate = new Date(imageInfo[0]['PATCH_UPDT']);
                    var patchUpDt = timeSince(myDate) + " ago";
                    $("#image-info-patch-updt").css("color", "green");
                }

                // Create HTML for patches that need to be reviewed
                var selectList = "";
                var fixedCount = 0;
                for (var key in compareList)
                {
                    if (compareList.hasOwnProperty(key))
                    {
                        var optionClass = "";

                        if (compareList[key]['DONE'] == "1")
                        {
                            optionClass = "greenSelect";
                            fixedCount = fixedCount + 1;
                        }

                        if (state.bibsKey[state.bibIndex] == key)
                        {
                            optionClass = "yellowBackgroundSelect";
                        }

                        selectList = selectList + "<option class='" + optionClass + "'>" + key + " - (" + compareList[key]['PERCENTAGE'] + "," + compareList[key]['FALSE_POS'] + ") - " + timeSince(new Date(compareList[key]['PATCH_UPDT'])) + "</option>";
                    }
                }

                $("#bib-labels").val(labels);
                $("#image-info-percent").html(percentage);
                $("#image-info-falsepos").html(falsePos);
                $("#image-info-source").html(source);
                $("#image-info-result").html(result);
                $("#image-info-patch-updt").html(patchUpDt);
                $("#patches-listbox").html(selectList);
                $("#files-fixed-count").html(fixedCount);
                $("#files-fixed-total").html(Object.keys(compareList).length);
                
                // Draw new bibs
                state.resetNewBibStyles();
                state.drawNewBibs();
                
                tbody.html(html);

            });
            
            this.valid = true;
        }
    }

    CanvasState.prototype.loadBibs = function(index)
    {
    	
        console.log("Calling load bibs...");
        key = this.bibsKey[index];
        this.bibImagePath = state.imagePath + "/" + key + IMAGE_EXT;
        this.bibs = [];  // clear the bibs array

        if (key in this.bibsDict)
        {    
            var resultValue = this.bibsDict[key];
            for (var x = 0; x < resultValue.length; x++)
            {
                var bib = resultValue[x];
                this.bibs.push(bib);
            }
        }       
    }
    
    CanvasState.prototype.loadBibsByName = function(fileName)
    {
        console.log("Calling load bibs...");
        key = fileName;
        this.bibImagePath = state.imagePath + "/" + key + IMAGE_EXT;
        this.bibs = [];  // clear the bibs array

        if (key in this.bibsDict)
        {    
            var resultValue = this.bibsDict[key];
            for (var x = 0; x < resultValue.length; x++)
            {
                var bib = resultValue[x];
                this.bibs.push(bib);
            }
        }       
    }


    // Creates an object with x and y defined, set to the mouse position relative to the state's canvas
    // If you wanna be super-correct this can be tricky, we have to worry about padding and borders
    CanvasState.prototype.getMouse = function(e) 
    {
        var element = this.imageCanvas, offsetX = 0, offsetY = 0, mx, my;

        // Compute the total offset
        if (element.offsetParent !== undefined) {
        do {
          offsetX += element.offsetLeft;
          offsetY += element.offsetTop;
        } while ((element = element.offsetParent));
        }

        // Add padding and border style widths to offset
        // Also add the <html> offsets in case there's a position:fixed bar
        offsetX += this.stylePaddingLeft + this.styleBorderLeft + this.htmlLeft;
        offsetY += this.stylePaddingTop + this.styleBorderTop + this.htmlTop;

        mx = e.pageX - offsetX;
        my = e.pageY - offsetY;

        // We return a simple javascript object (a hash) with x and y defined
        return {x: mx, y: my};
    }
    
    function updateProgress()
    {
        var bibsDonePercent = ((parseFloat(state.bibsDone) / parseFloat(state.bibsTotal)) * 100).toFixed(2);
        
        $("#bibs-total").html(state.bibsTotal);
        $("#bibs-complete").html(state.bibsDone);
        $("#images-total").html(state.imagesTotal);
        $("#images-complete").html(state.imagesDone);
        $("#progress-percent").html(bibsDonePercent);
    }

    function init() {
        console.log("Initiating state...");
        var imageCanvas = document.getElementById('image-canvas');
        var patchCanvas = document.getElementById('patch-canvas');
        state = new CanvasState(imageCanvas, patchCanvas);
        state.bibImagePath = "No images loaded";
        state.draw();
        //s.addShape(new Shape(40,40,50,50)); // The default is gray
        //s.addShape(new Shape(60,140,40,60, 'lightskyblue'));
        // Lets make some partially transparent
        //s.addShape(new Shape(80,150,60,30, 'rgba(127, 255, 212, .5)'));
        //s.addShape(new Shape(125,80,30,80, 'rgba(245, 222, 179, .7)'));
    }

    // Needs to be down here so all of the prototypes can load
    init();
    
    function SavePatches()
    {
        var csvContent = "data:text/csv;charset=utf-8,";
        
        for (var i=0; i<state.bibsKey.length; i++) {
            
            var key = state.bibsKey[i];
            if (key in state.bibsDict) {
                
                var bibsList = state.bibsDict[key];
                for (var j=0; j<bibsList.length; j++) {
                    var bib = bibsList[j];
                    
                    if (bib.mainImageKey == undefined) {
                        console.log("mainImageKey undefined at: " + j + ", key: " + key + ", index: " + i);
                        continue;
                    }
                    // Convert all properties to CSV
                    dataArray = [
                                 bib.patchImagePath,
                                 bib.isFound,
                                 bib.patchLabel,
                                 bib.patchScore1,
                                 bib.patchScore2,
                                 bib.patchScore3,
                                 bib.patchScore4,
                                 bib.patchScore5,
                                 bib.patchScore6,
                                 bib.isCorrect,
                                 bib.patchX,
                                 bib.patchY,
                                 bib.patchX2,
                                 bib.patchY2,
                                 bib.resizedImageW,
                                 bib.resizedImageH,
                                 bib.mainPatchImagePath,
                                 bib.mainLabel,
                                 bib.mainX,
                                 bib.mainY,
                                 bib.mainX2,
                                 bib.mainY2,
                                 bib.mainImageKey.replace(/[\n\r]+/g, ""),
                                 bib.committee,
                                 bib.isDone
                                ]
                
                    dataString = dataArray.join(",");
                    
                    csvContent += dataString + "\n";
                    
                }
            }
        }
        
        return csvContent;
    }
    
    function SaveNewPatches()
    {
        var csvContent = "data:text/csv;charset=utf-8,";
        
        for (var i=0; i<state.bibsKey.length; i++) {
            
            var key = state.bibsKey[i];
            if (key in state.newBibsDict) {
                
                var newBibs = state.newBibsDict[key];
                for (var j=0; j<newBibs.length; j++) {
                    var bib = newBibs[j];
                    
                    if (bib.sourceImage == undefined) {
                        console.log("sourceImage undefined at: " + j);
                        continue;
                    }
                    // Convert all properties to CSV
                    var x1 = Math.round(bib.startX)
                    var y1 = Math.round(bib.startY)
                    var x2 = Math.round(bib.w) + x1;
                    var y2 = Math.round(bib.h) + y1;
                    dataArray = [
                                 bib.sourceImage + IMAGE_EXT,
                                 bib.label,
                                 x1,
                                 y1,
                                 x2,
                                 y2,
                                 bib.sourceImageW,
                                 bib.sourceImageH
                                ]
                
                    dataString = dataArray.join(",");
                    
                    csvContent += dataString + "\n";
                    
                }
            }
        }
        
        return csvContent;
    }
    
    function SaveLabels()
    {
        var csvContent = "data:text/csv;charset=utf-8,";
        
        for (var i=0; i<state.bibsKey.length; i++) {
            
            var key = state.bibsKey[i];
            if (key in state.bibsLabelDict) {
                
                var labels = state.bibsLabelDict[key];
                
                dataArray = [
                             key,
                             labels
                            ]
            
                dataString = dataArray.join(",");
                
                csvContent += dataString + "\n";
                    
            }
        }
        
        return csvContent;
    }

    function UpdatePatchLabels()
    {
        var patchFileNamesList = [];
            
        state.bibs.forEach(function(bib) {
            patchFileNamesList.push(bib.patchImagePath.replace(IMAGE_EXT, ""));
        });

        for (var j=0; j<state.bibs.length; j++) {
            var bib = state.bibs[j];
            
            // Update data from DOM
            var patchFileName = bib.patchImagePath.replace(IMAGE_EXT, "");
            if ($.inArray(patchFileName, patchFileNamesList) >= 0) {
                newPatchLabel = $("#" + patchFileName).val();

                if (bib.mainLabel != newPatchLabel)
                {
                    bib.labelUpdated = true;
                }

                bib.mainLabel = newPatchLabel;
            }
        
            // Mark all bibs in this set as done
            bib.isDone = "true";
            state.bibsDone = state.bibsDone + 1;
        }
    }

    function GotoImage(fileName)
    {
        state.valid = false;
        state.bibIndex = state.bibsKey.indexOf(fileName);
        state.loadBibsByName(fileName);
        state.draw();
    }
    
    function GetImageInfoFromDB(imageKey)
    {
        var imageInfo = "";
        var imageSetSelection = $("#image-set").val();
        var loadCleanUpOnly = $("#load-only-cleanup-images").prop('checked');

        $.ajax({
            url: "functions.php",
            type: "POST",
            async: false,
            data: { action: "getImageInfo", imageSet: imageSetSelection, imageKey: imageKey, cleanUpOnly: loadCleanUpOnly },
            beforeSend: function()
            {
                $('#loadingImage').show();
            }

        })
        .done(function (msg) {
            if (msg.indexOf("error") > -1)
            {
                alert(msg);
                return;
            }

            imageInfo = msg;

        })
        .fail(function(error) {
            alert( "error: " + error );
        })
        .complete(function () {
            $('#loadingImage').hide();
        });

        return imageInfo;
    }

    function ExportPatchesToDB()
    {
        UpdatePatchLabels();

        var exportOnlyUpdatedBibs = $("#export-updated-bibs-checkbox").prop('checked');
        var imageSetSelection = $("#image-set").val();

        var l = state.bibs.length;
        var patches = [];
        for (var i = 0; i < l; i++) {
            var patch = state.bibs[i].patchImagePath;
            var label = state.bibs[i].mainLabel;
            var labelUpdated = state.bibs[i].labelUpdated;
            var mainImageKey = state.bibs[i].mainImageKey;

            if (exportOnlyUpdatedBibs == false || (exportOnlyUpdatedBibs && (labelUpdated || label == "-1")))
            {
                var patchObject = {"IMAGE": mainImageKey, "PATCH": patch, "LABEL": label}
                patches.push(patchObject);
            }
        }

        var jsonString = JSON.stringify(patches);

        $.ajax({
            url: "functions.php",
            type: "POST",
            async: true,
            data: { action: "exportPatches", imageSet: imageSetSelection, data: jsonString },
            beforeSend: function()
            {
                $('#loadingImage').show();
            }

        })
        .done(function (msg) {
            if (msg.indexOf("error") > -1)
            {
                alert(msg);
                return;
            }
            if (parseInt(msg) > 0)
            {
                alert("Patches have been successfully exported");
            }
            else
            {
                alert("Save Failed: " + msg);
            }

        })
        .fail(function(error) {
            alert( "error: " + error );
        })
        .complete(function () {
            $('#loadingImage').hide();
        });
    }

    function SaveLabelsToDB()
    {
        var labels = $("#bib-labels").val();
        var imageSetSelection = $("#image-set").val();
        var imageKey = state.bibsKey[state.bibIndex];

        $.ajax({
            url: "functions.php",
            type: "POST",
            async: true,
            data: { action: "saveLabel", imageSet: imageSetSelection, imageKey: imageKey, labels: labels },
            beforeSend: function()
            {
                $('#loadingImage').show();
            }

        })
        .done(function (msg) {
            if (msg.indexOf("error") > -1)
            {
                alert(msg);
                return;
            }
            if (parseInt(msg) > 0)
            {
                $("#label-checkmark").show();
            }
            else
            {
                alert("Save Failed: " + msg);
            }
        })
        .fail(function(error) {
            alert( "error: " + error );
        })
        .complete(function () {
            $('#loadingImage').hide();
        });
    }

    function MarkImageAsDoneInDB(isDone)
    {
        var imageSetSelection = $("#image-set").val();
        var imageKey = state.bibsKey[state.bibIndex];

        UpdatePatchLabels();

        $.ajax({
            url: "functions.php",
            type: "POST",
            async: true,
            data: { action: "updateImageStatus", imageSet: imageSetSelection, imageKey: imageKey, isDone: isDone },
            beforeSend: function()
            {
                $('#loadingImage').show();
            }

        })
        .done(function (msg) {
            if (msg.indexOf("error") > -1)
            {
                alert(msg);
                return;
            }
            if (parseInt(msg) > 0)
            {
                alert("Success!");
            }
            else
            {
                alert("Save Failed: " + msg);
            }
        })
        .fail(function(error) {
            alert( "error: " + error );
        })
        .complete(function () {
            $('#loadingImage').hide();
        });
    }

    // function UpdateLabels()
    // {
    // 	var newLabel = $("#bib-labels").val();
    	
    // 	var newList = newLabel.split(',');
    	
    // 	for (var i = 0; i < newList.length; i++)
    // 	{
    // 		if (newList[i] == "")
    // 		{
    // 			  newList.splice(i);	
    // 		}
    // 	}
    	
    // 	var key = state.bibsKey[state.bibIndex];
    //     state.bibsLabelDict[key] = newList;
        
    // }
    
    $("#load").click(function () {
        state.clearAllObjects();
        var imageSetSelection = $("#image-set").val();
        var imagePath = <?php echo '"' . $imagePath  . '"'?> + imageSetSelection + "/images";
        var patchPath = <?php echo '"' . $imagePath  . '"'?> + imageSetSelection + "/patches";
        state.imagePath = imagePath;
        state.patchPath = patchPath;

        // var labelReader = new FileReader();

        $.ajax({
            url: "functions.php",
            type: "POST",
            async: true,
            data: { action: "load", imageSet: imageSetSelection },
            beforeSend: function()
            {
                $('#loadingImage').show();
            }

        })
        .done(function (msg) {

            // Load the results detailed
            if (msg.indexOf("error") > -1)
            {
                alert(msg);
                return;
            }

            LoadDetailedCSV(msg, state, null);

            // if ($("#file-label-upload")[0].files[0] != undefined)
            // {
            //     labelReader.readAsText($("#file-label-upload")[0].files[0]);
            // }
        })
        .fail(function(error) {
            alert( "error: " + error );
        })
        .complete(function () {
            $('#loadingImage').hide();
        });
        
        // labelReader.onload = function (e) {
        //     console.log("Loading patch CSV output table...");
            
        //     var rows = e.target.result.split("\n");
        //     for (var i = 0; i < rows.length; i++) {
                
        //         var cells = rows[i].split(",");
        //         var key = cells[0];
                
        //         var newLabels = [];
        //         for (var x = 0; x < cells.length; x++)
        //         {
        //             if (x > 0)
        //             {
        //                 newLabels.push(cells[x]);
        //             }
        //         }
                
        //         state.bibsLabelDict[key] = newLabels;
        //     }
        // }
                   
    });
    
    $("#prev-image-button").click(function () {

        //UpdateLabels();
        $(".checkmark").hide();
        
        if (state.bibIndex == 0)
        {
            return false;
        }
        
        if (state.CheckForUnlabeledBibs() == false)
        {
            return false;
        }
        
        state.bibIndex = state.bibIndex - 1;

        state.valid = false;
        state.loadBibs(state.bibIndex);
        state.draw();
    });
    
    $("#next-image-button").click(function () {

    	//UpdateLabels();
        $(".checkmark").hide();
    	
        if (state.bibIndex == state.bibsKey.length - 1)
        {
            return false;
        }
        
        if (state.CheckForUnlabeledBibs() == false)
        {
            return false;
        }
        
        state.bibIndex = state.bibIndex + 1;
        
        state.valid = false;
        state.loadBibs(state.bibIndex);
        state.draw();
    });

    $("#done-next-image-button").click(function () {

    	//UpdateLabels();
        $(".checkmark").hide();
    	
        if (state.bibIndex >= state.bibsKey.length)
        {
        	alert("All done! No more images");
            return false;
        }
        
        if (state.CheckForUnlabeledBibs() == false)
        {
            return false;
        }
        
        // Update local copy
        UpdatePatchLabels();

        // Update database
        MarkImageAsDoneInDB(true);

        state.bibIndex = state.bibIndex + 1;
        
        state.imagesDone = state.imagesDone + 1;
        
        // Add progress info
        updateProgress();

        state.valid = false;
        
        // Draw the last one
        if (state.bibIndex == state.bibsKey.length)
            state.loadBibs(state.bibIndex - 1);
        else
        	state.loadBibs(state.bibIndex);
        state.draw();
    });
    
    $("#not-done-next-image-button").click(function () {
    	
        //UpdateLabels();
        $(".checkmark").hide();
    	
        state.bibIndex = state.bibIndex + 1;

        if (state.bibIndex >= state.bibsKey.length)
        {
            return false;
        }
        
        if (state.CheckForUnlabeledBibs() == false)
        {
            return false;
        }
        
        // Mark all bibs in this set as not done
        for (var i=0; i<state.bibs.length; i++) {
            state.bibs[i].isDone = "false";
            state.bibsDone = state.bibsDone - 1;
        }

        // Update database
        MarkImageAsDoneInDB(false);
        
        state.imagesDone = state.imagesDone - 1;

        state.valid = false;
        state.loadBibs(state.bibIndex);
        state.draw();
    });

    $("#done-image-button").click(function () {

        // Update local copy
        UpdatePatchLabels();

        // Update database
        MarkImageAsDoneInDB(true);

        state.imagesDone = state.imagesDone + 1;
        
        // Add progress info
        updateProgress();
    });

    $("#not-done-image-button").click(function () {

        // Update local copy
        UpdatePatchLabels();

        // Update database
        MarkImageAsDoneInDB(false);

        state.imagesDone = state.imagesDone - 1;
        
        // Add progress info
        updateProgress();
    });

    $("#save-labels-db-button").click(function () {

        SaveLabelsToDB();
    });

    $("#export-patches-db-button").click(function () {

        ExportPatchesToDB();
    });
    
    $("#save-csv-button").click(function () {
    	
        //UpdateLabels();
    	
    	var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.csv|.txt)$/;
    	var newSaveFileName = $("#new-file-save").val();
    	var finalFileName = "";
    	
        if (newSaveFileName == "") {
               alert("Doh! Enter new file name to save to.");
               return;
        }
        
        finalFileName = newSaveFileName;
        
        if (regex.test(finalFileName.toLowerCase())) 
        {
        	
            var csvContent = "";
        	var saveOption = $("input:radio[name=save-type]:checked").val();
        	
        	if (saveOption == undefined)
        	{
        		alert("Please select a file type to save");
        	}
        	else
        	{
            	if (saveOption == "patch"){
            		csvContent = SavePatches();
            	}
            	else if (saveOption == "label") {
            		csvContent = SaveLabels();
            	}
            	else if (saveOption == "newpatch") {
            		csvContent = SaveNewPatches();
            	}
            	
            	// remove the last newline
            	csvContent = csvContent.replace(/\n$/, "");
            	
            	var encodedUri = encodeURI(csvContent);
                //window.open(encodedUri);
                var link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", finalFileName);
                
                document.body.appendChild(link); // for firefox
                
                link.click();
    
                document.body.removeChild(link); // fore firefox
        	}
            
            
        } else {
            alert("Please enter a valid CSV file.");
            return;
        }
    });
    
    $("#image-go-button").click(function () {
        $(".checkmark").hide();
    	var fileName = $("#image-load-name").val();
        GotoImage(fileName);
    	
    });
    
    $("#clear-drawings-button").click(function () {
    	state.clearNewRects();
    });
    
    $("#delete-selected-drawing-button").click(function () {
        state.deleteSelectedDrawings();
    });

    $("#patches-listbox").dblclick(function () {
        $(".checkmark").hide();
        var selected = $("#patches-listbox").find(":selected").text();
        var imageName = selected.split(" - ")[0];
        GotoImage(imageName);
    });
    
    

});

</script>

<div id='loadingImage' style='display:none'>
  <img src='images/loading2.gif'/>
</div>
</body>
</html>
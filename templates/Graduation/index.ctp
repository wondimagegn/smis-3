<!--
<script src="http://www.exratione.com/assets/raphael.1.5.2.min.js" 
    type="text/javascript" charset="utf-8"></script> 
<script src="http://www.exratione.com/assets/g.raphael.0.4.1.min.js" 
    type="text/javascript" charset="utf-8"></script> 
<script src="http://www.exratione.com/assets/g.line.0.4.2.altered.js" 
    type="text/javascript" charset="utf-8"></script> 


<script type="text/javascript" src="/js/raphael.js"></script>
<script type="text/javascript" src="/js/g.raphael.js"></script>
<script type="text/javascript" src="/js/g.pie.js"></script>
<script type="text/javascript" src="/js/raphael_linechart.js"></script>
<script type="text/javascript" src="/js/g.line.js"></script>
<script type="text/javascript" src="/js/g.dot.js"></script>
<script type="text/javascript" src="/js/g.bar.js"></script>

<style type="text/css"> 
.chartWrapper {
  margin: 15px auto;
  width: 500px;
  height: 200px;
  text-align: center;
}
</style> 

<div id="simpleExample" class="chartWrapper"></div> 

<script type="text/javascript"> 
  var r = Raphael("simpleExample");
  var chart = r.g.linechart(
    10, 10,      // top left anchor
    490, 180,    // bottom right anchor
    [
      [1, 2, 3, 4, 5, 6, 7],        // red line x-values
      [3.5, 4.5, 5.5, 6.5, 7, 8]    // blue line x-values
    ], 
    [
      [12, 32, 23, 15, 17, 27, 22], // red line y-values
      [10, 20, 30, 25, 15, 28]      // blue line y-values
    ], 
    {
       nostroke: false,   // lines between points are drawn
       axis: "0 0 1 1",   // draw axes on the left and bottom
       symbol: "disc",    // use a filled circle as the point symbol
       smooth: true,      // curve the lines to smooth turns on the chart
       dash: "-",         // draw the lines dashed
       colors: [
         "#995555",       // the first line is red  
         "#555599"        // the second line is blue
       ]
     });
</script> 


















<script type="text/javascript">
// Creates canvas 320 × 200 at 10, 50
var paper = Raphael(10, 50, 320, 200);

// Creates circle at x = 50, y = 40, with radius 10
var circle = paper.circle(100, 100, 10);
// Sets the fill attribute of the circle to red (#f00)
circle.attr("fill", "#f00");

// Sets the stroke attribute of the circle to white
circle.attr("stroke", "#fff");




// Creates canvas 640 × 480 at 10, 50
var r = Raphael(10, 50, 40, 80);
// Creates pie chart at with center at 320, 200,
// radius 100 and data: [55, 20, 13, 32, 5, 1, 2]
r.g.piechart(320, 240, 100, [55, 20, 13, 32, 5, 1, 2]);
</script>






<div id="line-chart-holder"></div>
<table id="d2" style="display: none;">
    <tfoot>
        <tr>
            <th>3/02</th>
            <th>3/03</th>
            <th>3/09</th>
            <th>3/16</th>
        </tr>
    </tfoot>
    <tbody class="data">
        <tr>
            <td>70</td>
            <td>70</td>
            <td>210</td>
            <td>490</td>
        </tr>
    </tbody>
    <tbody class="line1">
        <tr>
            <td>70 Views</td>
            <td>70 Views</td>
            <td>210 Views</td>
            <td>490 Views</td>
        </tr>
    </tbody>
    <tbody class="line2">
        <tr>
            <td>Mar 2nd 2011</td>
            <td>Mar 3rd 2011</td>
            <td>Mar 9th 2011</td>
            <td>Mar 16th 2011</td>
        </tr>
    </tbody>
</table>

<script type="text/javascript">
    window.onload = function(){
        var w = 840; // you can make this dynamic so it fits as you would like
        var paper = Raphael("line-chart", w, 250); // init the raphael obj and give it a width plus height
        paper.lineChart({ // call the lineChart function
            data_holder: "d2", // find the table data source by id
            width: w, // pass in the same width
            show_area: true, // show the area
            x_labels_step: 3, // X axis labels step
            y_labels_count: 5,  // Y axis labels count
            mouse_coords: "rect", // rect (uses blanket mode) | circle (pinpoints the points)
            colors: {
                master: "#01A8F0" // set the line color
            }
        });
    };
</script>

-->

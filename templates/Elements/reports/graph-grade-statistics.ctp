<?php

function js_str($s)
{
    return '"' . addcslashes($s, "\0..\37\"\\") . '"';
}

function jsArray($arr)
{
    $temp = array_map("js_str", $arr);
    return '[' . implode(',', $temp) . ']';
}

function jsArrayData($arr)
{
    $string = "[";
    foreach ($arr as $key => $value) {
        $temp = array_map("js_str", $value);
        $string .= '[' . implode(',', $temp) . '],';
    }
    $string .= "]";
    //return '['.implode(',',$temp).']';
    return $string;
} ?>

<?php 
if (isset($gradeStatistics['graph']) && !empty($gradeStatistics['graph'])) { ?>
    <div class="panel panel-default">

        <div class="panel-body">
            <canvas id="bar" class="chart chart-bar" chart-data="data" chart-series="series" chart-labels="labels" chart-colors="colors" chart-options="options">

            </canvas>
        </div>
    </div>

    <script type="text/javascript">
        //"chart.js"
        var resapp = angular.module('resultEntryForm', ['ngRoute', 'chart.js']);

        resapp.controller('resultEntryFormCntrl', ['$scope', '$http', function($scope, $http) {
            $scope.options = {
                legend: {
                    display: true
                }
            };
            $scope.colors = ['#da7493', '#428bca'];
            $scope.labels = <?php echo jsArray($gradeStatistics['graph']['labels']); ?>;
            $scope.series = <?php echo jsArray($gradeStatistics['graph']['series']); ?>;
            $scope.data = <?php echo jsArrayData($gradeStatistics['graph']['data']); ?>;

        }]);
    </script>

    <?php 
} ?>
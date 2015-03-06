<?php

require_once 'Formulate.php';

//Basically, the whole code is encapsulated in this:
function form($str) {
    $f = new Formulate();
    $f->parse($str);
    if($f->isChanged()) {
        return $f->getResult();
    } else {
        return "No changes detected";
    }
}



//Let's roll with some tests

function test($objective, $src, $dst) {
    $result = form($src);

    if(trim($result) == trim($dst)) {
        echo "OK: `".$objective."`\n";
    } else {
        echo "---------TEST `".$objective."` FAILED----------\n";
        echo $src;
        echo "\n-------RESULT:--------------\n";
        echo $result;
        echo "\n";
    }
}



test("Multiple sheets", '<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td>{C2-C1=}</td><td></td><td>{A1+B1/(C2-A2) - 8 - (1 * 6)=}</td></tr>
</table>
<br><br><br><br>
<h1>Potato</h1>
<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>{A1+A2=}</td><td>8</td></tr>
</table>
','<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>7</td><td>8</td></tr>
<tr><td>{C2-C1=3}</td><td></td><td>{A1+B1/(C2-A2)-8-(1*6)=-9}</td></tr>
</table>
<br><br><br><br>
<h1>Potato</h1>
<table>
<tr><td>3</td><td>4</td><td>5</td></tr>
<tr><td>6</td><td>{A1+A2=9}</td><td>8</td></tr>
</table>');




test("Some other test", '
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=}</td></tr>
<tr><td>3</td><td>4</td><td>{A2+B2=}</td></tr>
<tr><td>6</td><td>7</td><td>{A3+B3=}</td></tr>
<tr><td>6</td><td>7</td><td>{A4+B4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=33}</td></tr>
<tr><td>3</td><td>4</td><td>{A2+B2=7}</td></tr>
<tr><td>6</td><td>7</td><td>{A3+B3=13}</td></tr>
<tr><td>6</td><td>7</td><td>{A4+B4=13}</td></tr>
</table>
');

test("Fractions",'
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=}</td></tr>
<tr><td>3</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=34.7833444}</td></tr>
<tr><td>3</td><td>4.5</td><td>{A2+B2=7.5}</td></tr>
<tr><td>6</td><td>7.21</td><td>{A3+B3=13.21}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
</table>
');


test(", in fractions", '
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4=35.6498944}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
</table>
');

test("Out of range", '
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4/B78=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C2+C3+C4/B78=#RANGE}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
</table>
');

test("Minimal formual", '
<table>
<tr><td>SUMA</td><td></td><td>{A5=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
<tr><td>17</td><td></td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A5=17}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
<tr><td>17</td><td></td><td></td></tr>
</table>
');

test("NAN", '
<table>
<tr><td>SUMA</td><td></td><td>{A5=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A5=#NAN}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');

test("Error in formula", '
<table>
<tr><td>SUMA</td><td></td><td>{A2 + potato()=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A2+potato()=#NAME}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');


test("Circular dependency", '
<table>
<tr><td>SUMA</td><td></td><td>{C1=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C1=#LOOP}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{A2+B2=7.93335}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{A4+B4=14.0733444}</td></tr>
<tr><td>potato</td><td></td><td></td></tr>
</table>
');

test("Multiple circular depenedencies", '
<table>
<tr><td>SUMA</td><td></td><td>{C4+5=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1+2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2+3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3+4=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{C4+5=#LOOP}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1+2=#LOOP}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2+3=#LOOP}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3+4=#LOOP}</td></tr>
</table>
');

test("Calculations again", '
<table>
<tr><td>SUMA</td><td></td><td>{A2+A3=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1+2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2+C1=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3+C1=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A2+A3=9.86655}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1+2=11.86655}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2+C1=21.7331}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3+C1=31.59965}</td></tr>
</table>
');

test("Some multiplications and divisions",'
<table>
<tr><td>SUMA</td><td></td><td>{A2+A3=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1*2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2/C1=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3-C1=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{A2+A3=9.86655}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1*2=19.7331}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2/C1=2}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3-C1=-7.86655}</td></tr>
</table>
');




test("Function: SUM",'
<table>
<tr><td>SUMA</td><td></td><td>{SUM(A2:B4)=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1*2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2/C1=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3-C1=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{SUM(A2:B4)=35.6498944}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1*2=71.2997888}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2/C1=2}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3-C1=-33.6498944}</td></tr>
</table>
');

test("Function: SUM with some formulas",'
<table>
<tr><td>SUMA</td><td></td><td>{SUM(A2:B4) + A3 - B4 * B2=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1*2=}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2/C1=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3-C1=}</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{SUM(A2:B4)+A3-B4*B2=8.1530944}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>{C1*2=16.3061888}</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{C2/C1=2}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>{C3-C1=-6.1530944}</td></tr>
</table>
');


test("Function: AVG",'
<table>
<tr><td>SUMA</td><td></td><td>{AVG(A2:B4)=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{AVG(A2:B4)=5.9416490666667}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
');


test("Function: Out of range",'
<table>
<tr><td>SUMA</td><td></td><td>{SUM(A2:F4)=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{SUM(A2:F4)=#RANGE}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
');

test("Function: NAN",'
<table>
<tr><td>SUMA</td><td></td><td>{SUM(A1:C4)=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{SUM(A1:C4)=#NAN}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td></td></tr>
<tr><td>6,4332</td><td>7.21</td><td></td></tr>
<tr><td>6.5333444</td><td>7.54</td><td></td></tr>
</table>
');


test("Function: Loop detection",'
<table>
<tr><td>SUMA</td><td></td><td>{SUM(C1:C4)=}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>0</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>3</td></tr>
</table>
','
<table>
<tr><td>SUMA</td><td></td><td>{SUM(C1:C4)=#LOOP}</td></tr>
<tr><td>3,43335</td><td>4.5</td><td>0</td></tr>
<tr><td>6,4332</td><td>7.21</td><td>{A3+B3=13.6432}</td></tr>
<tr><td>6.5333444</td><td>7.54</td><td>3</td></tr>
</table>
');

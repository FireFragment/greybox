<legend class="pure-u-1">Debatér <?php echo $_REQUEST["number"]; ?></legend>
<fieldset>
    <div class="pure-u-1 pure-u-md-1-4">
        <label for="name">Jméno</label>
        <input id="name" type="text" name="name-<?php echo $_REQUEST["number"]; ?>" required>
    </div>
    <div class="pure-u-1 pure-u-md-1-4">
        <label for="surname">Příjmení</label>
        <input id="surname" type="text" name="surname-<?php echo $_REQUEST["number"]; ?>" required>
    </div>
    <div class="pure-u-1 pure-u-md-1-4 pure-form-aligned">
        <label for="day">Datum narození</label>
        <select id="day" name="day-<?php echo $_REQUEST["number"]; ?>">
            <?php
                for ($i = 1; $i <= 31; $i++) {
                    echo "<option value=\"$i\">$i</option>";
                }
            ?>
        </select>
        <select id="month" name="month-<?php echo $_REQUEST["number"]; ?>">
            <?php
                $months = ["leden", "únor", "březen", "duben", "květen", "červen", "červenec", "srpen", "září", "říjen", "listopad", "prosinec"];
                for ($i = 1; $i <= 12; $i++) {
                    $j = $i-1;
                    echo "<option value=\"$i\">$months[$j]</option>";
                }
            ?>
        </select>
        <select id="year" name="year-<?php echo $_REQUEST["number"]; ?>">
            <?php
                for ($i = 0; $i <= 99; $i++) {
                    $j = 2018-$i;
                    echo "<option value=\"$j\">$j</option>";
                }
            ?>
        </select>
    </div>
    <div class="pure-u-1 pure-u-md-1-4">
        <label for="op">Číslo občanského průkazu</label>
        <input id="op" type="text" name="op-<?php echo $_REQUEST["number"]; ?>">
    </div>
    <div class="pure-u-1 pure-u-md-1-4">
        <label for="street">Ulice a číslo</label>
        <input id="street" type="text" class="smartform-street-and-number" name="street-<?php echo $_REQUEST["number"]; ?>" required>
    </div>
    <div class="pure-u-1 pure-u-md-1-4">
        <label for="city">Město</label>
        <input id="city" type="text" class="smartform-city" name="city-<?php echo $_REQUEST["number"]; ?>" required>
    </div>
    <div class="pure-u-1 pure-u-md-1-4">
        <label for="zip">PSČ</label>
        <input id="zip" type="text" class="smartform-zip" name="zip-<?php echo $_REQUEST["number"]; ?>" required>
    </div>
    <div class="pure-u-1 pure-u-md-1-4">
        <label for="comment">Poznámka</label>
        <textarea id="comment" type="text" name="note-<?php echo $_REQUEST["number"]; ?>" placeholder="například čas příjezdu/odjezdu, stravovací omezení apod."></textarea>
    </div>
</fieldset>
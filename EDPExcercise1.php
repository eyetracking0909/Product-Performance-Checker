  <?php
    session_start();

        $products = [
            1 => "Notebook",
            2 => "Ballpen",
            3 => "Pencil",
            4 => "Correction Tape",
            5 => "Ruler"
        ];
        $days = [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday"
        ];

    if (!isset($_SESSION["sales"])) {
        $_SESSION["sales"] = [];
        foreach ($products as $id => $name) {
            foreach ($days as $day) {
                $_SESSION["sales"][$id][$day] = null;
            }
        }
    }

    $selectedDay = $_GET["day"] ?? "Monday";
    $validOptions = array_merge($days, ["Weekly"]);

        if (!in_array($selectedDay, $validOptions)) {
            $selectedDay = "Monday";
        }

    if (isset($_POST["commit"])) {

        $productID = $_POST["product_id"];
        $day = $_POST["day"];
        $sales = $_POST["sales"];

        if (is_array($sales)) {
            $sales = $sales[$productID] ?? 0;
        }

    if (
        isset($products[$productID]) &&
        in_array($day, $days)
    ) {
        $sales = max(0, min(100, (int)$sales));
        $_SESSION["sales"][$productID][$day] = $sales;
    }
        header("Location: ?day=" . urlencode($day));
        exit;
    }

    if (isset($_POST["commit_all_day"])) {
        $day = $_POST["day"];

        if (in_array($day, $days)) {
            foreach ($products as $id => $name) {
                if (isset($_POST["sales"][$id])) {
                    $sales = max(0, min(100, (int)$_POST["sales"][$id]));
                    $_SESSION["sales"][$id][$day] = $sales;
                }
            }
        }

        header("Location: ?day=" . urlencode($day));
        exit;
    }
    
    if (isset($_POST["reset_day"])) {
        $day = $_POST["day"];
        if (in_array($day, $days)) {
            foreach ($products as $id => $name) {
                $_SESSION["sales"][$id][$day] = null;
            }
        }
        header("Location: ?day=" . urlencode($day));
        exit;
    }

    $allDataComplete = true;

    foreach ($products as $id => $name) {
        foreach ($days as $day) {
            if ($_SESSION["sales"][$id][$day] === null) {
                $allDataComplete = false;
            }
        }
    }

    function getPerformance($sales)
    {
        if ($sales >= 75) {
            return "High Performing";
        } elseif ($sales >= 50) {
            return "Moderate Performing";
        } elseif ($sales >= 25) {
            return "Low Performing";
        } else {
            return "Critically Low Performing";
        }
    }

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Product Performance Checker</title>
    </head>
    <body>
        <h1>Product Performance Checker</h1>
        
        <form method="GET">

            <label for="day">Select Day:</label>

            <select name="day" id="day" onchange="this.form.submit()">

                <?php foreach ($days as $day): ?>

                    <option
                        value="<?php echo $day; ?>"
                        <?php if ($selectedDay == $day) echo "selected"; ?>
                    >
                        <?php echo $day; ?>
                    </option>

                <?php endforeach; ?>
                
                <option
                    value="Weekly"
                    <?php
                    if ($selectedDay == "Weekly") echo "selected";
                    if (!$allDataComplete) echo "disabled";
                    ?>
                >
                    Weekly
                </option>
            </select>
        </form>

        <br>

        <?php if ($selectedDay != "Weekly"): ?>
            <h2>
                <?php echo $selectedDay; ?> Product Sales
            </h2>

            <form method="POST" id="day-controls-form">

                <input
                    type="hidden"
                    name="day"
                    value="<?php echo $selectedDay; ?>"
                >
                <button type="submit" name="reset_day">Reset <?php echo $selectedDay; ?></button>
                <button type="submit" name="commit_all_day" id="commit_all_day_btn">Commit All</button>
            </form>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const commitAllBtn = document.getElementById("commit_all_day_btn");

                    if (!commitAllBtn) {
                        return;
                    }

                    commitAllBtn.addEventListener("click", function (event) {
                        event.preventDefault();

                        const form = this.form;
                        const oldHiddenFields = form.querySelectorAll('input[name^="sales["]');
                        oldHiddenFields.forEach(function (field) {
                            field.remove();
                        });

                        document.querySelectorAll('input[name="sales"]').forEach(function (input) {
                            const productId = input.closest("form").querySelector('input[name="product_id"]').value;
                            const hiddenField = document.createElement("input");
                            hiddenField.type = "hidden";
                            hiddenField.name = "sales[" + productId + "]";
                            hiddenField.value = input.value;
                            form.appendChild(hiddenField);
                        });

                        const commitAllField = document.createElement("input");
                        commitAllField.type = "hidden";
                        commitAllField.name = "commit_all_day";
                        commitAllField.value = "1";
                        form.appendChild(commitAllField);

                        form.submit();
                    });
                });
            </script>

            <br>

            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Sales</th>
                    <th>Performance</th>
                    <th>Action</th>
                </tr>

                <?php foreach ($products as $id => $name): ?>

                <?php
                    $sales = $_SESSION["sales"][$id][$selectedDay];
                ?>

                    <tr>
                        <td>
                            <?php echo $id; ?>
                        </td>
                        <td>
                            <?php echo $name; ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?php echo $id; ?>"
                                >
                                <input
                                    type="hidden"
                                    name="day"
                                    value="<?php echo $selectedDay; ?>"
                                >
                                <input
                                    type="range"
                                    name="sales"
                                    min="0"
                                    max="100"
                                    value="<?php echo $sales !== null ? $sales : 0; ?>"
                                    oninput="this.nextElementSibling.value = this.value"
                                >
                                <output>
                                    <?php echo $sales !== null ? $sales : 0; ?>
                                </output>
                        </td>
                        <td>
                            <?php
                            if ($sales === null) {
                                echo "No data yet";
                            } else {
                                echo getPerformance($sales);
                            }
                            ?>
                        </td>
                        <td>
                                <button type="submit" name="commit">
                                    Commit
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <h2>Weekly Product Performance</h2>

            <table border="1">
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Weekly Average Sales</th>
                    <th>Performance</th>
                </tr>

                <?php foreach ($products as $id => $name): ?>

                <?php
                    $weeklySales = $_SESSION["sales"][$id];
                    $average = array_sum($weeklySales) / count($weeklySales);
                    $performance = getPerformance($average);
                    ?>

                    <tr>
                        <td>
                            <?php echo $id; ?>
                        </td>
                        <td>
                            <?php echo $name; ?>
                        </td>
                        <td>
                            <?php echo number_format($average, 2); ?>
                        </td>
                        <td>
                            <?php echo $performance; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </body>
</html>
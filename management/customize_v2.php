<?php
$page = 'customize';

$cssFilePath = '../assets/styles.css';
$currentCssContent = file_get_contents($cssFilePath);

// Define regular expressions to extract default values
function extract_css($variable)
{
    return "/$variable:\s*(.*);/";
}

// Initialize CSS variables to the current values in the styles.css
$cssVariables = [
    'body_font-family', 'wrapper_animation', 'wrapper_padding', 'wrapper_flex-direction',
    'h1_font-size', 'h1_font-weight', 'h1_letter-spacing', 'h1_line-height', 'h1_text-align', 'h1_color', 'h1_margin',
    'h2_font-size', 'h2_font-weight', 'h2_text-transform', 'h2_color', 'h2_text-align', 'h2_margin',
    'h3_font-size', 'h3_color', 'h3_font-weight', 'h3_line-height', 'h3_letter-spacing', 'h3-margin',
    'p_font-size', 'p_font-weight', 'p_text-align', 'p_color',
    'spacer_height',
    'button_padding', 'button_font-size', 'button_background-image', 'button_border-radius', 'button_border', 'button_width', 'button_color', 'button_letter-spacing', 'button_margin',
    'button_hover_color', 'button_hover_background-image', 'button_hover_transition', 'button_hover_transform',
    'progress_bar_width', 'progress_bar_height', 'progress_bar_background-color', 'progress_bar_border-radius', 'progress_bar_border-color', 'progress_bar_border-style', 'progress_bar_position', 'progress_bar-top', 'progress_bar-left',
    'progress_background-color', 'progress_border-radius',
    'q_container_background-color', 'q_container_border', 'q_container_padding', 'q_container_border-radius', 'q_container_letter-spacing', 'q_container_color',
    'next_font-weight', 'next_background-image', 'next_color',
    'product_container_background_image', 'product_container_border-radius', 'product_container_border', 'product_container_margin', 'product_container_width',
    'suggested_image_width', 'suggested_image_border-radius',
    'product_body_p_font-size', 'product_body_p_color', 'product_body_p_font-weight',
    'result_title_h3_font-weight', 'result_title_h3_font-size', 'result_title_h3_color', 'result_title_h3_margin'
];

// Extract default values
$current_value = [];
foreach ($cssVariables as $variable) {
    preg_match(extract_css("--$variable"), $currentCssContent, $matches);
    $current_value[$variable] = $matches[1] ?? '';
}

// Get form inputs or use default values
$postedValues = $_POST;
foreach ($cssVariables as $variable) {
    $$variable = $postedValues[$variable] ?? $current_value[$variable] ?? '';
}

// Construct the CSS content based on the form values
$cssContent = ":root {\n";
foreach ($cssVariables as $variable) {
    $cssContent .= "    --$variable: $$variable;\n";
}
$cssContent .= "}\n";

// Replace the relevant parts with the updated content
$newCssContent = preg_replace('/:root\s*\{[^}]*\}/s', $cssContent, $currentCssContent);

// Write the updated CSS content back to styles.css
file_put_contents($cssFilePath, $newCssContent);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <!-- Bootstrap 4.5.2 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/css/multi-select-tag.css">
    
    <title>Customize</title>

    <style>
        #preview {
        /* Ensure proper alignment of elements */
        display: flex;
        flex-direction: column;
    }
        #preview iframe {
            width: 100%;
            height: 100vh; /* Fill entire height */
            border: none; /* Remove border around iframe */
    }
        #iframe-container {
        /* Center the iframe horizontally and vertically */
        display: flex;
        justify-content: center;
        align-items: center;
        /* Set maximum width and height */
        max-width: 100%;
        max-height: 80vh; /* Adjust as needed */
        overflow: auto; /* Enable scrolling if content exceeds container */
    }
    </style>
</head>
<body>
    <div class="container-fluid">
            <!-- Forms column -->
            <div class="row">
            <div class="col-md-3">
                <h2>Customize Settings</h2>
                <form id="customize-form" method="post">
                    <div class="form-group">
                        <label for="button-color">Color:</label>
                        <input type="color" name="button-color" id="button-color" class="form-control" value="<?php echo $button_color; ?>">
                    </div>
                 
                    <div class="form-group">
                        <label>Width:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="button-width" value="50" <?php if ($button_width == '50') echo 'checked'; ?> class="form-check-input">
                            <label for="w50" class="form-check-label">50%</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="button-width" value="100" <?php if ($button_width == '100') echo 'checked'; ?> class="form-check-input">
                            <label for="w100" class="form-check-label">100%</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="button-margin-bottom">Margin Bottom:</label>
                        <input type="text" name="button-margin-bottom" id="button-margin-bottom" class="form-control" value="<?php echo $button_margin_bottom; ?>"><br><br>
                    </div>

                    <button type="submit" name="apply" class="btn btn-primary">Apply</button>
                </form>
            </div>
            <!-- Preview column -->
        
            <div class="col-md-9">
                <div id="preview">
                    <div class="row">
                        <div class="col-md-5">
                        <label for="page">Choose Page:</label>
                        <select name="page" id="page" class="form-control mb-3">
                            <option value="../index.php">Home</option>
                            <option value="../category-page.php">Category</option>
                            <option value="../quiz.php">Quiz</option>
                            <option value="../result.php">Result</option>
                    </select>
                        </div>
                        <div class="col-md-5">
                            <button type="button" class="btn btn-primary mr-2" onclick="setIframeSize('mobile')">Mobile</button>
                            <button type="button" class="btn btn-primary mr-2" onclick="setIframeSize('tablet')">Tablet</button>
                            <button type="button" class="btn btn-primary" onclick="setIframeSize('desktop')">Desktop</button>
                        </div>
                    </div>
                    
                    <div id="iframe-container">
                        <iframe id="preview-iframe" src="../index.php"></iframe>
                    </div>
                </div>
            </div>
            </div>
    </div>
   
    
   

    <script>
        var form = document.getElementById('customize-form');

        form.addEventListener('input', function(event) {
            // Get form values
            var color = document.getElementById('button-color').value;
            var width = document.querySelector('input[name="button-width"]:checked').value;
            var marginBottom = document.getElementById('button-margin-bottom').value;

            // Update CSS variables in the preview iframe
            var iframeDocument = document.getElementById('preview-iframe').contentDocument;
            var styleTag = iframeDocument.createElement('style');
            styleTag.textContent = `
                :root {
                    --button-color: ${color};
                    --button-width: ${width}%;
                    --button-margin-bottom: ${marginBottom};
                }
            `;
            iframeDocument.head.appendChild(styleTag);
        });


        var pageSelect = document.getElementById('page');
        var previewIframe = document.getElementById('preview-iframe');

         // Function to update iframe source based on selected page
         function updateIframeSrc() {
            var selectedPage = pageSelect.value;
            previewIframe.src = selectedPage;
        }

        // Add event listener to the page select for change event
        pageSelect.addEventListener('change', function(event) {
            // Update iframe source
            updateIframeSrc();
        });

        // Add event listener to the preview page select for change event
        previewPageSelect.addEventListener('change', function(event) {
            // Update iframe source
            previewIframe.src = this.value;
        });

        function setIframeSize(size) {
        var iframe = document.getElementById('preview-iframe');
        if (size === 'mobile') {
            iframe.style.width = '50%';
            iframe.style.height = '100vh'; // Adjust height percentage as needed
            iframe.style.maxWidth = '500px'; // Adjust maximum width as needed
            iframe.style.maxHeight = '850px'; // Adjust maximum height as needed
        } else if (size === 'tablet') {
            iframe.style.width = '70%';
            iframe.style.height = '100vh'; // Adjust height percentage as needed
            iframe.style.maxWidth = '800px'; // Adjust maximum width as needed
            iframe.style.maxHeight = '1200px'; // Adjust maximum height as needed
        } else if (size === 'desktop') {
            iframe.style.width = '100%';
            iframe.style.height = '100vh';
            iframe.style.maxWidth = '2300px'; // Adjust maximum width as needed
            iframe.style.maxHeight = '1200px'; // Adjust maximum height as needed
        }
    }
    </script>
</body>
</html>

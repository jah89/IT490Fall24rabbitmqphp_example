<!-- Success Alert -->
<div class="alert-success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
  <strong class="font-bold">Success!</strong>
  <span class="block sm:inline">Your action was successful.</span>
</div>

<!-- Error Alert -->
<div class="alert-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
  <strong class="font-bold">Error!</strong>
  <span class="block sm:inline">Something went wrong.</span>
</div>

<!-- Warning Alert -->
<div class="alert-warning bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
  <strong class="font-bold">Warning!</strong>
  <span class="block sm:inline">Be cautious with this action.</span>
</div>

<!-- Info Alert -->
<div class="alert-info bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
  <strong class="font-bold">Info!</strong>
  <span class="block sm:inline">Here is some information.</span>
</div>

<?php
function showAlert($type, $message) {
    $alertTypes = [
        'success' => 'bg-green-100 border border-green-400 text-green-700',
        'error' => 'bg-red-100 border border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border border-yellow-400 text-yellow-700',
        'info' => 'bg-blue-100 border border-blue-400 text-blue-700'
    ];

    $alertClass = $alertTypes[$type] ?? 'bg-gray-100 border border-gray-400 text-gray-700';

    echo "
    <div class=\"{$alertClass} px-4 py-3 rounded relative\" role=\"alert\">
        <strong class=\"font-bold\">". ucfirst($type) ."!</strong>
        <span class=\"block sm:inline\">{$message}</span>
        <button class=\"absolute top-0 bottom-0 right-0 px-4 py-3\" onclick=\"this.parentElement.style.display='none';\">
        <span class=\"text-sm\">DISMISS</span>
</button>

    </div>";
}

?>
<style>
/* The snackbar - position it at the bottom and in the middle of the screen */
#snackbar {
    visibility:hidden; /* Hidden by default. Visible on click */
    min-width: 250px; /* Set a default minimum width */
    margin-left: -125px; /* Divide value of min-width by 2 */

    color: #fff; /* White text color */
    text-align: center; /* Centered text */
    border-radius: 10px; /* Rounded borders */
    padding: 16px; /* Padding */
    position: fixed; /* Sit on top of the screen */
    z-index: 1; /* Add a z-index if needed */
    left: 50%; /* Center the snackbar */
    top: 90px; /* 30px from the bottom */
    border-radius:0.5rem;
    z-index:999;
}
/* Show the snackbar when clicking on a button (class added with JavaScript) */
.show {
    visibility: visible !important; /* Show the snackbar */
    /* Add animation: Take 0.5 seconds to fade in and out the snackbar.
    However, delay the fade out process for 2.5 seconds */
    -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
    animation: fadein 0.5s, fadeout 0.5s 2.5s;
    background-color: #333; /* Black background color */
}
.snackerror{
    visibility: visible !important; /* Show the snackbar */
    -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
    animation: fadein 0.5s, fadeout 0.5s 2.5s;
    background-color: #8b0505; /* Black background color */
}
@-webkit-keyframes fadein {
    from {top: 0; opacity: 0;}
    to {top: 90px; opacity: 1;}
}
@keyframes fadein {
    from {top: 0; opacity: 0;}
    to {top: 90px; opacity: 1;}
}
@-webkit-keyframes fadeout {
    from {top: 90px; opacity: 1;}
    to {top: 0; opacity: 0;}
}
@keyframes fadeout {
    from {top: 90px; opacity: 1;}
    to {top: 0; opacity: 0;}
}

</style>

<script>

</script>
<template>
<div id="snackbar">Some text some message..</div>

</template>
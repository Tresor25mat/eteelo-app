<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Calander</title>
    <link rel="stylesheet" href="calendrier/page.css">
    <link rel="stylesheet" href="calendrier/style.css">
    <link rel="stylesheet" href="calendrier/theme.css">
    <meta name="viewport" content="width=device-width">
  </head>
  <body>
    <!-- <h1>Calendar Plugin Demo</h1> -->
    <main>
      <div class="calendar-wrapper" id="calendar-wrapper" style="border: 1px solid #0076C8; font-size: 12px"></div>
<!--       <div id="editor"></div> -->
    </main>
<!--     <footer>made with <span style="color: red">♥</span> by <a target="_blank"
        href="https://github.com/wrick17">wrick17</a>.
      Check out the code and docs in <a target="_blank"
        href="https://github.com/wrick17/calendar-plugin">github</a></footer> -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="calendrier/calendar.min.js"></script>
    <!-- <script src="calendar.js"></script> -->
    <script
      src="https://unpkg.com/codeflask/build/codeflask.min.js"></script>
    <script type="text/javascript">
      var config = `
function selectDate(date) {
  $('#calendar-wrapper').updateCalendarOptions({
    date: date
  });
  console.log(calendar.getSelectedDate());
}

var defaultConfig = {
  weekDayLength: 1,
  date: '08/05/2021',
  onClickDate: selectDate,
  showYearDropdown: true,
  startOnMonday: true,
};

var calendar = $('#calendar-wrapper').calendar(defaultConfig);
console.log(calendar.getSelectedDate());
`;
      eval(config);
      const flask = new CodeFlask('#editor', { 
        language: 'js', 
        lineNumbers: true 
      });
      flask.updateCode(config);
      flask.onUpdate((code) => {
        try {
          eval(code);
        } catch(e) {}
      });
    </script>
  </body>
</html>

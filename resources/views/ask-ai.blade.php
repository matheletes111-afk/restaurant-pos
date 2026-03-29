<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Ask AI</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
</head>

<body data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">

      <!-- Breadcrumb -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Ask AI — Restaurant Assistant</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item" aria-current="page">Ask AI</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Breadcrumb end -->

      <div class="row">
        <div class="col-sm-12">
          <div class="card">

            <div class="card-header">
              <h5>Ask anything related to your restaurant business</h5>
            </div>

            <div class="card-body">

              <!-- AI Question Box -->
              <div class="form-group">
                <label class="fw-bold">Your Question</label>
                <textarea id="question" class="form-control" rows="3" placeholder=""></textarea>
              </div>

              <button id="ask-btn" class="btn btn-primary" style="float: right;">
                Ask AI
              </button>
              <div style="clear: both;"></div>

              <!-- Output -->
              <div id="response-box" class="card p-3 mt-4 d-none" style="background: #f8f9fa; border: 1px solid #ddd;">
                <h6 class="fw-bold mb-2">AI Response:</h6>
                <p id="answer" style="white-space: pre-line; font-size: 15px;"></p>
              </div>

            </div>

          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @include('includes.script')

  <script>
    // Rotating Placeholder
    let placeholders = [
      "How to politely handle a customer complaint?",
      "Create a promotion message for weekend offer",
      "Best menu pricing strategy for more profit?",
      "How to reduce food wastage cost?",
      "What are best selling dish ideas for family crowd?"
    ];

    let index = 0;
    setInterval(() => {
      document.getElementById("question").placeholder = placeholders[index];
      index = (index + 1) % placeholders.length;
    }, 2000);

    // Ask AI AJAX
    $('#ask-btn').on('click', function () {
      let question = $('#question').val();
      if (question.trim() == "") return;

      $('#ask-btn').text('Thinking...').prop('disabled', true);

      $.ajax({
        url: "{{ route('ask-ai.send') }}",
        type: "POST",
        data: {
          _token: "{{ csrf_token() }}",
          question: question
        },
        success: function (data) {
          $('#response-box').removeClass('d-none');
          $('#answer').text(data.answer);
          $('html, body').animate({ scrollTop: $(document).height() }, 600);
        },
        error: function () {
          alert("Something went wrong, please try again!");
        },
        complete: function () {
          $('#ask-btn').text('Ask AI').prop('disabled', false);
        }
      });
    });
  </script>

</body>
</html>

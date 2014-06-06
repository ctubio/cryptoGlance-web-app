!function (root, $) {

  var RigCollection = function () {
    this.collection = []
  }

  RigCollection.prototype.add = function(rig) {
    this.collection.push(rig)
  }

  Rig.prototype.update = function () {}

  RigCollection.prototype.generateOverview = function() {

    var overview = $('#overview')
    var overviewTableData = ''

    $.ajax({
      type: 'post',
      data : {
        type: 'miners',
        action: 'overview'
      },
      url: 'ajax.php',
      dataType: 'json'
    })

    $('#overview .panel-body-overview div table tbody').append()
  }

  // $(document).on('update.rigs', update)

  root.RigCollection = RigCollection

}(window, window.jQuery)

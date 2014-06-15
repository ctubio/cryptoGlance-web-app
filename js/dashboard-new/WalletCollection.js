/**

  TODO:
  - scaffold WalletCollection class

**/

!function (root, $) {

  /*=================================================================
  =            WalletCollection Class/Object/Constructor            =
  =================================================================*/

  var WalletCollection = function () {
    this.collection = []
  }

  /*-----  End of WalletCollection Class/Object/Constructor  ------*/

  /*=======================================================
  =            WalletCollection Public Methods            =
  =======================================================*/

  WalletCollection.prototype.start = function () {
    var _self = this

    _self._getData(function (wallets) {
      wallets.forEach(function (wallet, index) {
        _self._add(index)
      })

      setInterval(function () {
        _self._update()
      }, window.interval)
    })
  }

  /*-----  End of WalletCollection Public Methods  ------*/


  /*========================================================
  =            WalletCollection Private Methods            =
  ========================================================*/

  WalletCollection.prototype._add = function (walletId) {
    this.collection.push(new Wallet(walletId))
  }

  WalletCollection.prototype._update = function () {
    var _self = this
    _self._getData(function (wallets) {
      _self.collection.forEach(function (wallet, index) {
        wallet.update(wallets[index])
      })
    })
  }

  WalletCollection.prototype._getData = function (callback) {
    $.ajax({
      url: 'ajax.php?type=wallets&action=update',
      dataType: 'json',
      statusCode: {
        401: function() {
          window.location.assign('login.php');
        }
      }
    })
    .done(callback)
  }

  /*-----  End of WalletCollection Private Methods  ------*/


  /*===============================================
  =            Export WalletCollection            =
  ===============================================*/

  root.WalletCollection = WalletCollection

  /*-----  End of Export WalletCollection  ------*/

}(window, window.jQuery)

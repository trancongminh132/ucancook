var diagnose = function(boxy) {
    alert("Position: " + boxy.getPosition() +
          "\nSize: " + boxy.getSize() +
          "\nContent size: " + boxy.getContentSize() +
          "\nCenter: " + boxy.getCenter());
};

$(function() {
  
  Boxy.DEFAULTS.title = '123mua';
  
  //
  // Diagnostics
  
  $('#diagnostics').click(function() {
      new Boxy("<div><a href='#' onclick='diagnose(Boxy.get(this));'>Diagnose</a></div>");
      return false;
  });

  //
  // Set content
  
  var setContent = null;
  $('#set-content-open').click(function() {
      setContent = new Boxy(
        "<div style='background-color:red'>This is content</div>", {
          behaviours: function(c) {
            c.hover(function() {
              $(this).css('backgroundColor', 'green');
            }, function() {
              $(this).css('backgroundColor', 'pink');
            });
          }
        }
      );
      return false;
  });
  $('#set-content').click(function() {
      setContent.setContent("<div style='background-color:blue'>This is new content</div>");
      return false;
  });
  
  //
  // Callbacks
  
  $('#after-hide').click(function() {
      new Boxy("<div>Test content</div>", {
        afterHide: function() {
          alert('after hide called');
        }
      });
      return false;
  });
  
  $('#before-unload').click(function() {
      new Boxy("<div>Test content</div>", {
        beforeUnload: function() {
          alert('before unload called');
        },
        unloadOnHide: true
      });
      return false;
  });
  
  $('#before-unload-no-auto-unload').click(function() {
      new Boxy("<div>Test content</div>", {
        beforeUnload: function() {
          alert('should not see this');
        },
        unloadOnHide: false
      });
      return false;
  });
  
  $('#after-drop').click(function() {
      new Boxy("<div>Test content</div>", {
        afterDrop: function() {
          alert('after drop: ' + this.getPosition());
        },
        draggable: true
      });
      return false;
  });
  
  $('#after-show').click(function() {
      new Boxy("<div>Test content</div>", {
        afterShow: function() {
          alert('after show: ' + this.getPosition());
        }
      });
      return false;
  });
  
  //
  // Z-index
  
  var zIndex = null;
  $('#z-index').click(function() {
      zIndex = new Boxy(
        "<div>Test content</div>", { clickToFront: true }
      );
      return false;
  });
  
  $('#z-index-latest').click(function() {
      zIndex.toTop();
      return false;
  });
  
  //
  // Modals
  
  function newModal() {
      new Boxy("<div><a href='#'>Open a stacked modal</a> | <a href='#' onclick='alert(Boxy.isModalVisible()); return false;'>test for modal dialog</a></div>", {
        modal: true, behaviours: function(c) {
          c.find('a:first').click(function() {
            newModal();
          });
        }
      });
  };
  
  $('#modal').click(newModal);
  
  //
  // No-show
  
  var noShow;
  $('#no-show').click(function() {
      noShow = new Boxy("<div>content</div>", {show: false});
      return false;
  });
  
  $('#no-show-now').click(function() {
      noShow.show();
      return false;
  });
  
  // Actuator
  
  $('#actuator').click(function() {
      var ele = $('#actuator-toggle')[0];
      new Boxy("<div>test content</div>", {actuator: ele, show: false});
      return false;
  });
  $('#actuator-toggle').click(function() {
      Boxy.linkedTo(this).toggle();
      return false;
  });
  
});
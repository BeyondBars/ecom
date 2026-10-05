import swal from 'sweetalert'
import { merge } from 'lodash'

export default (options = {}) => {
  return swal(merge({
    icon: false,
    buttons: {
      cancel: {
        text: 'Cancel',
        value: false,
        visible: false,
        className: 'btn btn-outline-dark',
        closeModal: true
      },
      confirm: {
        text: 'Yes',
        value: true,
        visible: true,
        className: 'btn btn-danger',
        closeModal: true
      }
    }
  }, options))
}

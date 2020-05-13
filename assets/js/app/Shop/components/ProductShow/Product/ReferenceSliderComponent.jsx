import React from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

class ReferenceSliderComponent extends React.Component {
  constructor (props) {
    super(props)
    this.getTarget = this.getTarget.bind(this)
  }

  getId () {
    return 'carousel-reference-' + this.props.currentReference.id
  }

  getItemClass (index) {
    let className = 'carousel-item'

    if (index === this.getActivatedIndex()) {
      className += ' active'
    }
    return className
  }

  getIndicatorClass (index) {
    if (index === this.getActivatedIndex()) {
      return 'active'
    }
    return ''
  }

  getActivatedIndex () {
    return this.props.activatedIndexByReference[this.props.currentReference.id] || 0
  }

  getTarget () {
    return '#' + this.getId()
  }

  componentDidMount () {
    $(this.getTarget).on('slid.bs.carousel', (e) => {
      this.props.changeActivatedIndex(this.props.currentReference, e.to)
    })
  }

  render () {
    return (
      <div id={this.getId()} className='carousel slide carousel-fade carousel-product' data-ride='carousel'>
        <div className='carousel-inner'>
          {
            this.props.currentReference.images.map((image, index) =>
              <div key={image.id} className={this.getItemClass(index)}>
                <img src={image.url} alt={image.alt}/>
              </div>
            )
          }
        </div>

        {
          this.props.currentReference.images.length > 1 &&
          <a href={this.getTarget()} className='carousel-control-prev' data-slide='prev' role='button'>
            <span aria-hidden='true' className='carousel-control-prev-icon'/>
            <span className='sr-only'>Previous</span>
          </a>
        }

        {
          this.props.currentReference.images.length > 1 &&
          <a href={this.getTarget()} className='carousel-control-next' data-slide='next' role='button'>
            <span aria-hidden='true' className='carousel-control-next-icon'/>
            <span className='sr-only'>Next</span>
          </a>
        }
        {
          this.props.currentReference.images.length > 1 &&
          <ol className='carousel-indicators'>
            {
              this.props.currentReference.images.map((image, index) =>
                <li
                  className={this.getIndicatorClass(index)} data-slide-to={index}
                  data-target={this.getTarget()} key={index}
                >
                  <img src={image.urlThumbnail} alt={image.altThumbnail}/>
                </li>
              )
            }
          </ol>
        }
      </div>
    )
  }
}

ReferenceSliderComponent.propTypes = {
  currentReference: PropTypes.shape({
    id: PropTypes.number.isRequired,
    label: PropTypes.string.isRequired,
    images: PropTypes.arrayOf(PropTypes.shape({
      id: PropTypes.number.isRequired,
      url: PropTypes.string.isRequired,
      alt: PropTypes.string.isRequired,
      urlThumbnail: PropTypes.string.isRequired,
      altThumbnail: PropTypes.string.isRequired
    })),
    unitPriceIncludingTaxes: PropTypes.number.isRequired
  }),
  activatedIndexByReference: PropTypes.object.isRequired,

  changeActivatedIndex: PropTypes.func.isRequired
}

const mapStateToProps = (state) => {
  return {}
}

const mapDispatchToProps = (dispatch) => {
  return {
    changeActivatedIndex: (reference, index) => dispatch({
      type: 'UPDATE_ACTIVATED_SLIDER_INDEX_BY_REFERENCE',
      reference,
      index
    })
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(ReferenceSliderComponent)

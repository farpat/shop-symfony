import React, { useEffect } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

function ReferenceSliderComponent ({ currentReference, activatedIndexByReference, changeActivatedIndex }) {

  const getId = function () {
    return `carousel-reference-${currentReference.id}`
  }

  const getItemClass = function (index) {
    let className = 'carousel-item'

    if (index === getActivatedIndex()) {
      className += ' active'
    }
    return className
  }

  const getIndicatorClass = function (index) {
    if (index === getActivatedIndex()) {
      return 'active'
    }
    return ''
  }

  const getActivatedIndex = function () {
    return activatedIndexByReference[currentReference.id] || 0
  }

  const getTarget = function () {
    return '#' + getId()
  }

  useEffect(() => {
    $(getTarget()).on('slid.bs.carousel', event => changeActivatedIndex(currentReference, event.to))
  })

  return (
    <div id={getId()} className='carousel slide carousel-fade carousel-product' data-ride='carousel'>
      <div className='carousel-inner'>
        {
          currentReference.images.map((image, index) =>
            <div key={image.id} className={getItemClass(index)}>
              <img src={image.url} alt={image.alt}/>
            </div>
          )
        }
      </div>

      {
        currentReference.images.length > 1 &&
        <a href={getTarget()} className='carousel-control-prev' data-slide='prev' role='button'>
          <span aria-hidden='true' className='carousel-control-prev-icon'/>
          <span className='sr-only'>Previous</span>
        </a>
      }

      {
        currentReference.images.length > 1 &&
        <a href={getTarget()} className='carousel-control-next' data-slide='next' role='button'>
          <span aria-hidden='true' className='carousel-control-next-icon'/>
          <span className='sr-only'>Next</span>
        </a>
      }
      {
        currentReference.images.length > 1 &&
        <ol className='carousel-indicators'>
          {
            currentReference.images.map((image, index) =>
              <li
                className={getIndicatorClass(index)} data-slide-to={index}
                data-target={getTarget()} key={index}
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

ReferenceSliderComponent.propTypes = {
  currentReference         : PropTypes.shape({
    id                     : PropTypes.number.isRequired,
    label                  : PropTypes.string.isRequired,
    images                 : PropTypes.arrayOf(PropTypes.shape({
      id          : PropTypes.number.isRequired,
      url         : PropTypes.string.isRequired,
      alt         : PropTypes.string.isRequired,
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

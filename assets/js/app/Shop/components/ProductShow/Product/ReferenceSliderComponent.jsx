import React, { useEffect, useState } from 'react'
import PropTypes from 'prop-types'
import { connect } from 'react-redux'

function ReferenceSliderComponent ({ currentReference }) {
  const [activatedIndex, setActivatedIndex] = useState(0)

  const getId = function () {
    return `carousel-reference-${currentReference.id}`
  }

  const getTarget = function () {
    return '#' + getId()
  }

  useEffect(() => {
    setActivatedIndex(0)

    const updateActivatedIndex = function (event) {
      event.preventDefault()
      setActivatedIndex(event.to)
    }

    $(getTarget()).on('slid.bs.carousel', updateActivatedIndex)

    return () => $(getTarget()).off('slid.bs.carousel', updateActivatedIndex)
  }, [currentReference])

  return (
    <>
      <div id={getId()} className='carousel slide carousel-fade carousel-product' data-ride='carousel'>
        <div className='carousel-inner'>
          {
            currentReference.images.map((image, index) =>
              <div key={image.id} className={'carousel-item' + (index === activatedIndex ? ' active' : '')}>
                <img src={image.url} alt={image.alt}/>
              </div>
            )
          }
        </div>

        {
          currentReference.images.length > 1 &&
          <>
            <a href={getTarget()} className='carousel-control-prev' data-slide='prev' role='button'>
              <span aria-hidden='true' className='carousel-control-prev-icon'/>
              <span className='sr-only'>Previous</span>
            </a>
            <a href={getTarget()} className='carousel-control-next' data-slide='next' role='button'>
              <span aria-hidden='true' className='carousel-control-next-icon'/>
              <span className='sr-only'>Next</span>
            </a>
          </>
        }
      </div>
      {
        currentReference.images.length > 1 &&
        <ol className='carousel-indicators carousel-product-indicators'>
          {
            currentReference.images.map((image, index) =>
              <li className={activatedIndex === index ? 'active' : ''} data-slide-to={index} data-target={getTarget()}
                  key={index}>
                <img src={image.urlThumbnail} alt={image.altThumbnail}/>
              </li>
            )
          }
        </ol>
      }
    </>
  )
}

ReferenceSliderComponent.propTypes = {
  currentReference: PropTypes.shape({
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
}

export default ReferenceSliderComponent
